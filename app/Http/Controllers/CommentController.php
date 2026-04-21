<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    private $comment;

    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    public function store(Request $request, $post_id)
    {
        # 1. validate the request
        $request->validate([
            'comment_body' . $post_id => 'required|max:150'
        ],
        [
            'comment_body'.$post_id.'.required' => 'You cannot submit an empty comment.',
            'comment_body'.$post_id.'.max' => 'Your comment must not exceed 150 characters.',
        ]);

        # 2. store the comment data
        $this->comment->body = $request->{'comment_body' . $post_id};
        $this->comment->user_id = Auth::user()->id;
        $this->comment->post_id = $post_id;
        $this->comment->save();

        # --- ここから追加：通知の保存 ---
        $post = \App\Models\Post::findOrFail($post_id);

        // 自分の投稿へのコメントでなければ、投稿者に通知を送る
        if ($post->user_id != Auth::id()) {
            $notification = new \App\Models\Notification();
            $notification->from_user_id = Auth::id();    // コメントした人
            $notification->to_user_id   = $post->user_id; // 投稿した人（通知を受け取る人）
            $notification->post_id       = $post_id;
            $notification->type          = 'comment';
            $notification->save();
        }
        # --- 追加ここまで ---

        # 3. redirect back to the post show page
        return redirect()->back();
    }

    
    public function destroy($id)
    {
        $comment = $this->comment->findOrFail($id);

        if ($comment->user_id !== Auth::id()) {
            return redirect()->back();
    }

        $comment->delete();

            return redirect()->back();
    }

    
}
