<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Category;
use App\Models\Repost;
use App\Models\Hashtag;

class HomeController extends Controller
{
    private $post;
    private $user;

    public function __construct(Post $post, User $user)
    {
        $this->post = $post;
        $this->user = $user;
    }

    public function index()
    {
        $home_posts = $this->getHomePosts();
        $suggested_users = $this->getSuggestedUsers();

        return view('users.home')->with('home_posts', $home_posts)
                  ->with('suggested_users', $suggested_users);
    }

    private function getHomePosts()
    {
        // 1. 自分がフォローしている人のIDリストを取得
        $following_ids = Auth::user()->following()->pluck('following_id')->toArray();

        // 2. 通常の投稿（自分 + フォロー中）を取得
        $regular_posts = Post::where('status', 'published')
            ->where(function($query) use ($following_ids) {
                $query->whereIn('user_id', $following_ids)
                    ->orWhere('user_id', Auth::id());
            })
            ->get();

        // 3. リポスト（フォロー中の人がリポストしたもの）を取得
        // ここで「リポストされた投稿」そのものを取得します
        $reposts = Repost::whereIn('user_id', $following_ids)
            ->with('post') // 投稿内容も一緒に取得
            ->get();

        // 4. 両方を合体させて日付順に並び替える
        // 通常の投稿は created_at、リポストはリポストした時間（created_at）で並べます
        $combined = collect();

        foreach ($regular_posts as $post) {
            $post->display_time = $post->created_at; // 並び替え用の基準時間
            $post->is_repost_view = false;
            $combined->push($post);
        }

        foreach ($reposts as $repost) {
            $post = $repost->post;
            // 元の投稿が非公開などの場合はスキップ
            if (!$post || $post->status !== 'published') continue;

            // 【重要】リポストとして別個体を作る
            $new_repost_instance = clone $post;
            $new_repost_instance->display_time = $repost->created_at; // リポストした時間を基準にする
            $new_repost_instance->is_repost_view = true;
            $new_repost_instance->reposter_name = $repost->user->name;
            $new_repost_instance->reposter_avatar = $repost->user->avatar;
            $combined->push($new_repost_instance);
        }

        // 最新順に並び替えて返す
        return $combined->sortByDesc('display_time')->values();
    }

    private function getSuggestedUsers()
    {
        $all_users = $this->user->all()->except(Auth::user()->id);

        $suggested_users = []; // holds all the users that we are NOT following

        foreach($all_users as $user){
            if(!$user->isFollowed()){
                $suggested_users[] = $user;
            }
        }

        return array_slice($suggested_users, 0, 5);
    }

    public function suggestions()
{
    $all_users = $this->user->all()->except(Auth::id());
    $suggested_users = [];

    foreach ($all_users as $user) {

        if (!$user->isFollowed()) {
            $suggested_users[] = $user;
        }
    }

    return view('users.suggestions')->with('suggested_users', $suggested_users);
}

    public function search(Request $request)
    {
        $keyword = trim($request->search);

        if ($keyword === '') {
            return redirect()->back();
        }

        // 🔽 #から始まる → ハッシュタグ検索
        if (str_starts_with($keyword, '#')) {
            $tagName = strtolower(ltrim($keyword, '#')); // ← ★この1行が大事
            return redirect()->route('tags.show', $tagName);
        }

        // 🔽 People検索（今まで通り）
        $users = $this->user
            ->where('name','like','%'.$keyword.'%')
            ->get();

        return view('users.search')
            ->with('users', $users)
            ->with('search', $keyword);
    }

 public function searchCategory(Request $request)
    {

    // --- 2. 投稿（カテゴリー）の検索 ---
    $posts_query = Post::query()->where('status', 'published');

    // カテゴリーがプルダウンで選択されている場合、絞り込みを行う
    if ($request->category) {
        $posts_query->whereHas('categoryPost', function($query) use ($request) {
            $query->where('category_id', $request->category);
        });
    }

    $posts = $posts_query->latest()->get();

    // --- 3. ビューに結果をまとめて渡す ---
    return view('users.search_category')
        ->with('posts', $posts)      // 検索/絞り込みされた投稿
        ->with('search', $request->search); // 検索ワード（表示用）        

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

public function recommendations()
{
    $recommend_posts = $this->getRecommendPosts();

    return view('users.recommendations') // 新しく作るBladeファイル名
            ->with('recommend_posts', $recommend_posts);
}


}
