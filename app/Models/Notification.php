<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    // これを追加することで、保存が許可されます！
    protected $fillable = [
        'from_user_id', 
        'to_user_id', 
        'post_id', 
        'type', 
        'is_read'
    ];

    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_user_id')->constrained('users')->onDelete('cascade'); // 行動した人
            $table->foreignId('to_user_id')->constrained('users')->onDelete('cascade');   // 通知を受け取る人
            $table->foreignId('post_id')->nullable()->constrained('posts')->onDelete('cascade'); // どの投稿にか
            $table->string('type'); // 'like', 'follow', 'comment' など
            $table->boolean('is_read')->default(false); // 既読フラグ
            $table->timestamps();
        });
    }

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }
}