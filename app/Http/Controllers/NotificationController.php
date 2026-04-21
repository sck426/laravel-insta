<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
    $notifications = Notification::where('to_user_id', Auth::id())
                        ->latest()
                        ->get();

    // 画面を開いたらすべて「既読」にする
    Notification::where('to_user_id', Auth::id())->update(['is_read' => true]);

    return view('users.notifications.index')->with('notifications', $notifications);
    }

    public function destroy($id)
    {
        // 自分の通知であること、かつ存在するものであることを確認して削除
        $notification = Notification::where('to_user_id', auth()->user()->id)
                                    ->where('id', $id)
                                    ->firstOrFail();
        $notification->delete();

        return redirect()->back()->with('success', 'Notification deleted.');
    }

}
