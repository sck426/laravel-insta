<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hashtag;

class HashtagController extends Controller
{
     public function show($name)
    {
        $name = strtolower($name);

        $tag = Hashtag::where('name', $name)->first();

        $posts = $tag
        ? $tag->posts()
            ->whereNull('deleted_at') // アーカイブ除外
            ->with('user')
            ->latest()
            ->get()
        : collect();

        return view('users.tags.show', compact('tag', 'posts','name'));
    }
}


