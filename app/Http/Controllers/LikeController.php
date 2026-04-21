<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Like;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use App\Models\Post;         

class LikeController extends Controller
{
    private $like;

    public function __construct(Like $like)
    {
        $this->like = $like;
    }

    # Store a like
    public function store($post_id)
    {
        $this->like->user_id = Auth::user()->id;
        $this->like->post_id = $post_id;
        $this->like->save();

        // --- ここで通知を作成！ ---
        $post = Post::find($post_id);
        
        // 自分の投稿へのいいねじゃなければ通知
        if ($post->user_id != Auth::id()) {
            $notification = new Notification();
            $notification->from_user_id = Auth::id();
            $notification->to_user_id   = $post->user_id;
            $notification->post_id       = $post_id;
            $notification->type          = 'like';
            $notification->save();
        }

        return redirect()->back();
    }


    # Destroy a like (Unlike)
    public function destroy($post_id)
    {
        $this->like->where('user_id', Auth::user()->id)
                   ->where('post_id', $post_id)
                   ->delete();

        return back();
    }

    # Display liked posts list
    public function index()
    {
        $user_id = Auth::id();
        $likes = $this->like->where('user_id', $user_id)
            ->with('post')
            ->get();

        return view('users.likes.index')->with('likes', $likes);
    }

    
}
