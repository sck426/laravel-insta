<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Repost;
use Illuminate\Support\Facades\Auth;

class RepostController extends Controller
{
    private $post;
    private $repost;

    public function __construct(Post $post,Repost $repost)
    {
        $this->post = $post;
        $this->repost = $repost;
    }
    
    public function store(Post $post)
    {
        Repost::firstOrCreate([
            'user_id' => Auth::id(),
            'post_id' => $post->id,
        ]);

        return back();
    }

    public function destroy(Post $post)
    {
        Repost::where('user_id', Auth::id())
            ->where('post_id', $post->id)
            ->delete();

        return back();
    }
}
