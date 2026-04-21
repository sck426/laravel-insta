<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Follow;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class FollowController extends Controller
{
    private $follow;

    public function __construct(Follow $follow)
    {
        $this->follow = $follow;
    }

    # follow
   public function store($user_id)
    {
        // 1. フォロー関係の保存
        $this->follow->follower_id = Auth::user()->id; // 自分
        $this->follow->following_id = $user_id;        // 相手
        $this->follow->save();

        // 2. ★ここに通知作成を追加！
        // 自分が自分をフォローした時に通知がいかないように一応チェック
        if (Auth::id() != $user_id) {
            Notification::create([
                'from_user_id' => Auth::id(), // フォローした人（自分）
                'to_user_id'   => $user_id,    // フォローされた人（相手）
                'type'         => 'follow',    // 通知の種類
                'post_id'      => null,        // フォローなので投稿IDは不要
                'is_read'      => false,
            ]);
        }

        return back();
    }

    # unfollow
    public function destroy($user_id)
    {
        $this->follow->where('follower_id', Auth::user()->id)
            ->where('following_id', $user_id)
            ->delete();

        return back();
    }
}
