<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ChatRoom;
use App\Models\Message;
use App\Models\Post;
use App\Models\Repost;

class User extends Authenticatable
{

    const ADMIN_ROLE_ID = 1;
    const USER_ROLE_ID = 2;
    
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;
    // use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    # USER - POST
    # a user has many posts
    public function posts()
    {
        return $this->hasMany(Post::class)->latest();
    }

    # to get all the followers of a user
    public function followers()
    {
        return $this->hasMany(Follow::class,'following_id');
    }

    # to get all the users that the user is following
    public function following()
    {
        return $this->hasMany(Follow::class,'follower_id');
    }


    #return TRUE if the Auth user is already following the user
    public function isFollowed()
    {
        return $this->followers()->where('follower_id', Auth::user()->id)->exists();
    }

    public function messages() {
        return $this->hasMany(Message::class); // ユーザーはたくさんのメッセージを送る
    }

    public function chatRooms() {
        return $this->belongsToMany(ChatRoom::class); // ユーザーは複数のチャットに参加する
    }

    public function reposts() {
        return $this->hasMany(Repost::class);
    }

    public function repostedPosts(){
        return $this->belongsToMany(Post::class,'reposts')->withTimestamps();
    }

    public function unreadMessages()
    {
        // 自分が参加しているチャットルームのIDをすべて取得
        $myRoomIds = $this->chatRooms()->pluck('chat_rooms.id');

        // それらのルームに届いたメッセージのうち、「自分以外が送信者」かつ「未読(is_read=0)」のものを返す
        return \App\Models\Message::whereIn('chat_room_id', $myRoomIds)
                    ->where('user_id', '!=', $this->id)
                    ->where('is_read', 0);
    }

    public function unreadMessagesFrom($senderId)
    {
        // 既存の unreadMessages() の結果に対して、さらに送信者を絞り込む
        return $this->unreadMessages()
                    ->where('user_id', $senderId) // 送信者がその人のものだけ
                    ->count();
    }

}
