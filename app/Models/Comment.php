<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    # COMMENT - USER
    # to get the owner of the comment
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
}
