<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    private $message; 

    
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function store(Request $request,$chat_room_id)
    {
        $request->validate([
            'body' => 'required|max:100'
        ]);

        Message::create([
            'chat_room_id' => $chat_room_id,
            'user_id' => Auth::id(),
            'body' => $request->body,
        ]);

        return back();
    }
}
