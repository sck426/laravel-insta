<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
class ProfileController extends Controller
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function show($id)
    {
        // 1. $id が数字ならIDで検索、そうでなければ名前(name)で検索
        if (is_numeric($id)) {
            $user = $this->user->findOrFail($id);
        } else {
            // メンション（名前）でアクセスされた場合
            $user = $this->user->where('name', $id)->firstOrFail();
        }

        // 2. 投稿の取得
        $posts = $user->posts()
            ->whereNull('deleted_at')
            ->where('status', '!=', 'archived')
            ->latest()
            ->get();

        // 3. リポストした投稿の取得
        $reposted_posts = $user->repostedPosts()
            ->where('status', 'published')
            ->whereNull('posts.deleted_at')
            ->latest('reposts.created_at')
            ->get();

        return view('users.profile.show', compact('user', 'posts', 'reposted_posts'));
    }

    public function edit()
    {
        $user = $this->user->findOrFail(Auth::user()->id);

        return view('users.profile.edit')->with('user',$user);
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|max:50',
            'email' => 'required|email|max:50|unique:users,email,' .Auth::user()->id,
            'avatar' => 'mimes:jpeg,jpg,png,gif|max:1048',
            'introduction' => 'max:100',
            'new_password'     => 'nullable|min:8|confirmed',
        ], [
             'new_password.confirmed' => 'The passwords do not match. Please enter them again.'
        ]);

            $user = $this->user->findOrFail(Auth::user()->id); 
            
            $user->name = $request->name;
            $user->email = $request->email;
            $user->introduction = $request->introduction;


        // ヘッダー画像の保存
        if ($request->hasFile('cover_photo')) {
            $user->cover_photo = 'data:image/' . $request->cover_photo->extension() . ';base64,' . base64_encode(file_get_contents($request->cover_photo));
        }
        if ($request->avatar) {
            $user->avatar = 'data:image/' . $request->avatar->extension() . ';base64,' . base64_encode(file_get_contents($request->avatar));
        }
        if($request->new_password){
            $user->password = Hash::make($request->new_password);
        }

        $user->save(); 

        return redirect()->route('profile.show', Auth::user()->id)
                         ->with('success', 'Profile updated successfully!');
    }

    public function followers($id)
    {
        $user = $this->user->findOrFail($id);

        return view('users.profile.followers')->with('user', $user);
    }

    public function following($id)
    {
        $user = $this->user->findOrFail($id);
        
        return view('users.profile.following')->with('user', $user);
    }
}
