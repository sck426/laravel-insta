<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use App\Models\Repost;
use App\Models\User;

class Post extends Model
{
    use SoftDeletes;
    
    #POST - USER
    # a post belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
    

    # POST - CATEGORYPOST
    # to get all the categories of a post
    public function categoryPost()
    {
        return $this->hasMany(CategoryPost::class);
    } 

    # POST - COMMENT
    # a post has many commnets
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    #POST- LIKES
    #a post has many likes
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    #return TRUE if the Auth user liked the post
    public function isliked()
    {
        return $this->likes()->where('user_id', Auth::user()->id)->exists();
    }

    public function reposts()
    {
        return $this->hasMany(Repost::class);
    }

    public function reposters()
    {
        return $this->belongsToMany(User::class,'reposts')->withTimestamps();
    }

    public function isReposted()
    {
        // reposters（リポストしたユーザー一覧）の中に、自分のIDが含まれているか
        return $this->reposters()->where('user_id', auth()->id())->exists();
    }

    public function hashtags()
    {
        return $this->belongsToMany(Hashtag::class, 'hashtag_post');
    }

}

