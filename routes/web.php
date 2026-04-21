<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\PostsController;
use App\Http\Controllers\Admin\PostsController as AdminPostsController;
use App\Http\Controllers\Admin\CategoriesController;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\ChatRoomController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\RepostController;
use App\Http\Controllers\HashtagController;
use App\Models\Repost;
use App\Http\Controllers\NotificationController;



Auth::routes();

Route::middleware('auth')->group(function () {
    Route::get('search', [HomeController::class,'search'])->name('search');
    Route::get('/search/category', [HomeController::class, 'searchCategory'])->name('search.category');
    Route::get('/post/drafts', [PostController::class, 'drafts'])->name('post.drafts');

    // 通知一覧画面
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');

    // 特定の通知を既読にする、または一括既読にするルート（今の実装に合わせて）
    Route::patch('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.readAll');

    // 通知の個別削除用
    Route::delete('/notifications/{id}/destroy', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    // Archive
    // アーカイブ一覧ページを表示する (ナビバー用)
    Route::get('/post/archive', [PostController::class, 'archiveIndex'])->name('post.archive_index');

    // 投稿をアーカイブに設定する (詳細画面のボタン用)
    Route::patch('/post/{id}/archive', [PostController::class, 'archive'])->name('post.archive_exec');

    // アーカイブを解除する
    Route::patch('/post/{id}/unarchive', [PostController::class, 'unarchive'])->name('post.unarchive');
    
    //Hashtag
    Route::get('/tag/{name}', [HashtagController::class, 'show'])
    ->name('tags.show');


    // Home
    Route::get('/', [HomeController::class, 'index'])->name('index');
    Route::get('/suggestions', [HomeController::class, 'suggestions'])->name('suggestions');
    Route::get('/recommendations', [HomeController::class, 'recommendations'])->name('recommendations');

    // Posts
    Route::get('/post/create', [PostController::class, 'create'])->name('post.create');
    Route::post('/post/store', [PostController::class, 'store'])->name('post.store');
    Route::get('/post/{id}/show', [PostController::class, 'show'])->name('post.show');
    Route::get('/post/{id}/edit', [PostController::class, 'edit'])->name('post.edit');
    Route::patch('/post/{id}/update', [PostController::class, 'update'])->name('post.update');
    Route::delete('/post/{id}', [PostController::class, 'destroy'])->name('post.destroy');

    // Comments
    Route::post('/comment/{post_id}/store', [CommentController::class, 'store'])->name('comment.store');
    Route::delete('/comment/{comment_id}/destroy', [CommentController::class, 'destroy'])->name('comment.destroy');

    // User
    Route::get('/profile/{id}/show', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/{user_id}/followers', [ProfileController::class, 'followers'])->name('profile.followers');
    Route::get('/profile/{user_id}/following', [ProfileController::class, 'following'])->name('profile.following');


    // like
    Route::post('/like/{post_id}/store', [LikeController::class,'store'])->name('like.store');
    Route::delete('/like/{post_id}/destroy', [LikeController::class,'destroy'])->name('like.destroy');
    Route::get('/likes',[LikeController::class,'index'])->name('like.index');

    // follow
    Route::post('/follow/{user_id}/store',[FollowController::class,'store'])->name('follow.store');
    Route::delete('/follow/{user_id}/destroy',[FollowController::class,'destroy'])->name('follow.destroy');

    // chat
    Route::get('/chat',[ChatRoomController::class,'index'])->name('chat.index');
    Route::post('/chat/{user_id}/store',[ChatRoomController::class,'store'])->name('chat.store');
    Route::get('/chat/{id}/show',[ChatRoomController::class,'show'])->name('chat.show');
    Route::post('/message/{chat_room_id}', [MessageController::class, 'store'])->name('message.store');

    // mention
    Route::get('/post/mentions', [PostController::class, 'mentions'])->name('post.mentions');

    // repost
    Route::post('/repost/{post}/store',[RepostController::class,'store'])->name('repost.store');
    Route::delete('/repost/{post}/destroy',[RepostController::class,'destroy'])->name('repost.destroy');

    # admin
    Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'admin'], function(){
  
    //users
    Route::get('/users', [UsersController::class,'index'])->name('users');
    Route::delete('/users/{id}/deactivate', [UsersController::class,'deactivate'])->name('users.deactivate');
    Route::patch('/users/{id}/activate', [UsersController::class,'activate'])->name('users.activate');
   

    // posts
    Route::get('/posts', [AdminPostsController::class,'index'])->name('posts');
    Route::delete('/posts/{id}/hide', [AdminPostsController::class, 'hide'])->name('posts.hide');
    Route::patch('/posts/{id}/unhide', [AdminPostsController::class, 'unhide'])->name('posts.unhide');

    // categories
    Route::get('/categories', [CategoriesController::class, 'index'])->name('categories'); 
    Route::post('/categories/store', [CategoriesController::class, 'store'])->name('categories.store'); 
    Route::patch('/categories/{id}/update', [CategoriesController::class, 'update'])->name('categories.update'); 
    Route::delete('/categories/{id}/destroy', [CategoriesController::class, 'destroy'])->name('categories.destroy');   


});

  
});
