<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatRoom;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class ChatRoomController extends Controller
{
    private $message;
    private $user;
    private $chatRoom;

    public function __construct(Message $message, User $user, ChatRoom $chat_room)
    {
        $this->user = $user;
        $this->message = $message;
        $this->chat_room = $chat_room;
    }

    public function index()
    {
      $all_users = $this->user->all();
      return view ('users.chats.list')->with('all_users',$all_users);
    }

    public function store($user_id)
{
    // 1. ログインユーザーと相手のユーザーが両方参加している部屋を探す
    $chat_room = Auth::user()->chatRooms()->whereHas('users', function($query) use ($user_id) {
        $query->where('user_id', $user_id);
    })->first();

    // 2. もし部屋がなければ新しく作る
    if (!$chat_room) {
        // chat_roomsテーブルに新しいレコードを作成
        $chat_room = ChatRoom::create();

        // 中間テーブルに自分と相手を紐付ける (attachを使用)
        $chat_room->users()->attach([Auth::user()->id, $user_id]);
    }

    // 3. 部屋のIDを持って、個別チャット画面へリダイレクト
    return redirect()->route('chat.show', $chat_room->id);
}

    public function show($id)
    {
        // --- 修正箇所ここから ---
        // このチャットルーム($id)の中で、自分(Auth::id)以外が送った未読メッセージをすべて既読にする
        Message::where('chat_room_id', $id)
            ->where('user_id', '!=', Auth::id())
            ->where('is_read', false) // 未読のものだけを対象にする
            ->update(['is_read' => true]);
        // --- 修正箇所ここまで ---

        $chat_room = $this->chat_room->findOrFail($id);

        // メッセージを取得（送信者の情報も一緒に読み込む）
        $messages = $chat_room->messages()->with('user')->oldest()->get();

        // 相手ユーザー（自分以外）を取得
        $partner = $chat_room->users()
                ->where('users.id', '!=', Auth::id())
                ->first();

        return view('users.chats.show', compact('chat_room', 'messages', 'partner'));
    }

    
}
