<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Hashtag;


class PostController extends Controller
{
    private $post;
    private $category;
    private $user;

    public function __construct(Post $post, Category $category, User $user)
    {
        $this->post = $post;
        $this->category = $category;
        $this->user = $user;
    }

    public function create()
    {
        $all_categories = $this->category->all();

        return view('users.posts.create')->with('all_categories', $all_categories);
    }

    public function store(Request $request)
    {
        $isDraft = $request->action === 'draft';

        // 1) validate
        $request->validate([
            'description' => 'required|max:1000',
            'category'    => ($isDraft ? 'nullable' : 'required') . '|array|between:1,3',
            'category.*'  => 'exists:categories,id',
            'image'       => ($isDraft ? 'nullable' : 'required') . '|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // 2) store post
        $post = new Post();
        $post->user_id = Auth::id();
        $post->description = $request->description;
        $post->status = $isDraft ? 'draft' : 'published';

        if ($request->hasFile('image')) {
            $post->image = 'data:image/'.$request->image->extension().';base64,'.base64_encode(file_get_contents($request->image));
        }

        $post->save();

        // 3) save categories
        if (!empty($request->category)) {
            $category_post = [];
            foreach ($request->category as $category_id) {
                $category_post[] = ['category_id' => $category_id];
            }
            $post->categoryPost()->createMany($category_post);
        }

        // 4) hashtags
        preg_match_all('/#([A-Za-z0-9_]+)/', $request->description, $matches);
        $tagNames = array_unique(array_map('strtolower', $matches[1]));

        $hashtagIds = [];
        foreach ($tagNames as $name) {
            $hashtagIds[] = Hashtag::firstOrCreate(['name' => $name])->id;
        }
        $post->hashtags()->sync($hashtagIds);

        // 5) ★メンション通知の追加
        // 公開設定（published）のときのみ通知を飛ばす
        if (!$isDraft) {
            preg_match_all('/@([A-Za-z0-9_]+)/', $request->description, $mentionMatches);
        $mentionedNames = array_unique($mentionMatches[1]);

        foreach ($mentionedNames as $name) {
            $user = User::where('name', $name)->first();

            if ($user && $user->id !== Auth::id()) {
                // すでにこの投稿でこの人にメンション通知を送っていないかチェック
                $exists = \App\Models\Notification::where('post_id', $post->id)
                            ->where('to_user_id', $user->id)
                            ->where('type', 'mention')
                            ->exists();

                if (!$exists) {
                    $notification = new \App\Models\Notification();
                    $notification->from_user_id = Auth::id();
                    $notification->to_user_id   = $user->id;
                    $notification->post_id      = $post->id;
                    $notification->type         = 'mention';
                    $notification->save();
                }
            }
        }

        // 6) redirect
        return $isDraft
            ? redirect()->route('post.drafts')
            : redirect()->route('index');
    }
}

    public function drafts()
    {
        $drafts = Post::where('user_id', Auth::user()->id)
                    ->where('status', 'draft')
                    ->latest()
                    ->get();

        return view('users.drafts.index')->with('drafts', $drafts);
    }

    public function show($id)
    {
        $post = Post::withTrashed()->findOrFail($id);

        return view('users.posts.show')->with('post', $post);
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);

        $post->categoryPost()->delete();

        $post->delete();

        return redirect()->route('index');
    }  

    public function edit($id)
    {
        $post = $this->post->findOrFail($id);

        // if Auth user is NOT the owner of the post, redirect to home
        if (Auth::user()->id !== $post->user->id){
            return redirect()->route('index');
        }

        $all_categories = $this->category->all();

        // get all categories of the post, save in an array
        $selected_categories = [];

        foreach ($post->categoryPost as $category_post) {
            $selected_categories[] = $category_post->category_id;
        }


        return view('users.posts.edit')->with('post',$post)
                    ->with('all_categories', $all_categories)
                    ->with('selected_categories', $selected_categories);
    }

    public function update(Request $request, $id)
    {
        $isDraft = $request->action === 'draft';

        // 1) validate
        $request->validate([
            'description' => 'required|max:1000',

            // 公開ならカテゴリ必須、下書きなら任意
            'category'    => ($isDraft ? 'nullable' : 'required') . '|array|between:1,3',
            'category.*'  => 'exists:categories,id',

            // 画像：下書きは任意 / 公開は「画像変更するなら」形式チェック
            // ※「公開は必ず画像必須」にしたいなら store と同じく required を付ける
            'image'       => ($isDraft ? 'nullable' : 'nullable') . '|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // 2) update post
        $post = $this->post->findOrFail($id);

        // 本人チェック（入れた方が安全）
        if ($post->user_id !== Auth::id()) {
            abort(403);
        }

        $post->description = $request->description;
        $post->status = $isDraft ? 'draft' : 'published';

        if ($request->hasFile('image')) {
            $post->image = 'data:image/'.$request->image->extension()
                .';base64,'.base64_encode(file_get_contents($request->image));
        }

        $post->save();

        // 3) categories（付け替え）
        $post->categoryPost()->delete();

        if (!empty($request->category)) {
            $category_post = [];
            foreach ($request->category as $category_id) {
                $category_post[] = ['category_id' => $category_id];
            }
            $post->categoryPost()->createMany($category_post);
        }

        // 4) hashtags（★ここが今回の目的：中間テーブルを更新）
        preg_match_all('/#([A-Za-z0-9_]+)/', $request->description, $matches);
        $tagNames = array_unique(array_map('strtolower', $matches[1]));

        $hashtagIds = [];
        foreach ($tagNames as $name) {
            $hashtagIds[] = Hashtag::firstOrCreate(['name' => $name])->id;
        }

        // 古い紐付けは消えて、新しい紐付けに置き換わる
        $post->hashtags()->sync($hashtagIds);

        // 5) redirect
        return $isDraft
            ? redirect()->route('post.drafts')
            : redirect()->route('index');
    }

    public function recommendations()
    {
        $recommend_posts = $this->getRecommendPosts();

        return view('users.recommendations') // 新しく作るBladeファイル名
                ->with('recommend_posts', $recommend_posts);
    }

    private function getRecommendPosts()
   {
    // 1. 自分以外の全ユーザーを取得（ログインユーザーを除く）
    $all_users = $this->user->all()->except(Auth::user()->id);
    $not_followed_user_ids = [];

    // 2. フォローしていないユーザーのIDだけを抽出
    foreach ($all_users as $user) {
        if (!$user->isFollowed()) {
            $not_followed_user_ids[] = $user->id;
        }
    }

    // 3. そのユーザーたちの投稿を取得（最新順、ステータスが公開のもの）
    // whereIn を使って、抽出したIDリストに一致する投稿を取得します
    return $this->post->whereIn('user_id', $not_followed_user_ids)
                      ->where('status', 'published')
                      ->latest()
                      ->get();
    }

    /**
     * 1. 投稿をアーカイブする処理
     */
    public function archive($id)
    {
        $post = $this->post->findOrFail($id);

        // 本人確認
        if ($post->user_id !== Auth::id()) {
            return redirect()->back();
        }

        // ステータスを 'archived' に変更して保存
        $post->status = 'archived';
        $post->save();

        return redirect()->back();
    }

    /**
     * 2. アーカイブした投稿の一覧を表示する処理
     */
    public function archiveIndex()
    {
        // 自分の投稿で、status が 'archived' のものを取得
        $archived_posts = $this->post->where('user_id', Auth::id())
                                     ->where('status', 'archived')
                                     ->latest()
                                     ->get();

        return view('users.posts.archive')->with('archived_posts', $archived_posts);
    }

    /**
     * 3. アーカイブを解除する（公開に戻す）処理
     */
    public function unarchive($id)
    {
        $post = $this->post->findOrFail($id);

        // 本人確認
        if ($post->user_id !== Auth::id()) {
            return redirect()->back();
        }

        // ステータスを 'published'（公開）に戻す
        $post->status = 'published';
        $post->save();

        return redirect()->back();
    }

    public function mentions()
    {
        // 自分の名前を取得（例: "Taro"）
        $myName = auth()->user()->name;

        // descriptionの中に "@自分の名前" が含まれている投稿を取得
        // % を使って前後の文字列を無視して検索します
        $all_posts = Post::where('description', 'LIKE', "%@{$myName}%")
                    ->latest()
                    ->paginate(10); // ページネーションをつけるとSNSっぽくなります

        return view('users.posts.mentions') // 次で作るビューを指定
                ->with('all_posts', $all_posts);
    }
}

