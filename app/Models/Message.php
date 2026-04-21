<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\ChatRoom;

class Message extends Model
{
    protected $fillable = [
    'chat_room_id',
    'user_id',
    'body',
    ];
    
    public function chatRoom() {
        return $this->belongsTo(ChatRoom::class); // メッセージは1つの部屋に属する
    }

    public function user() {
        return $this->belongsTo(User::class); // メッセージは1人のユーザーが書いたもの
    }
}
