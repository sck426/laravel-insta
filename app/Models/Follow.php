<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    public $timestamps = false;

    # get the details of the follower
    public function follower()
    {
        return $this->belongsTo(User::class,'follower_id')->withTrashed();
    }

    # get the details of the user that the user is following
    public function following()
    {
        return $this->belongsTo(User::class,'following_id')->withTrashed();
    }
}

