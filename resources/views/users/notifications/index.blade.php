@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="fw-bold mb-4">Notifications</h3>
    @forelse($notifications as $notification)
        <div class="d-flex align-items-center bg-white p-3 mb-2 shadow-sm rounded">
            {{-- 相手のアイコン --}}
            <div class="me-3">
                @if($notification->fromUser->avatar)
                    <img src="{{ $notification->fromUser->avatar }}" class="rounded-circle avatar-sm" style="width: 45px; height: 45px; object-fit: cover;">
                @else
                    <i class="fas fa-circle-user fa-3x text-secondary"></i>
                @endif
            </div>

            <div class="flex-grow-1">
                <strong>{{ $notification->fromUser->name }}</strong> 
                
                @if($notification->type == 'like')
                    liked your post.
                @elseif($notification->type == 'follow')
                    started following you.
                @elseif($notification->type == 'comment')
                    commented on your post. <i class="fa-solid fa-comment text-secondary"></i>
                    <div class="p-2 bg-light rounded mt-1 border-start border-primary border-4">
                        <a href="{{ route('post.show', $notification->post_id) }}" class="text-decoration-none text-dark small">
                            @php
                                $latestComment = $notification->post->comments()
                                                    ->where('user_id', $notification->from_user_id)
                                                    ->latest()
                                                    ->first();
                            @endphp
                            @if($latestComment)
                                <span class="text-muted italic">"{{ Str::limit($latestComment->body, 40) }}"</span>
                            @else
                                <span class="text-muted">Click to view the comment</span>
                            @endif
                        </a>
                    </div>
                @elseif($notification->type == 'mention')
                    mentioned you in a post. <i class="fa-solid fa-at text-primary"></i>
                    <div class="p-2 bg-light rounded mt-1 border-start border-info border-4">
                        <a href="{{ route('post.show', $notification->post_id) }}" class="text-decoration-none text-dark small">
                            <span class="text-muted">"{{ Str::limit($notification->post->description, 40) }}"</span>
                        </a>
                    </div>
                @endif
                
                <p class="text-muted small mb-0 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
            </div>

            {{-- 右端：投稿画像またはフォローボタン --}}
            <div class="ms-3 d-flex align-items-center">
                @if(in_array($notification->type, ['like', 'comment', 'mention']) && $notification->post_id)
                    <a href="{{ route('post.show', $notification->post_id) }}">
                        <img src="{{ $notification->post->image }}" 
                             alt="Post Image" 
                             style="width: 45px; height: 45px; object-fit: cover;" 
                             class="rounded shadow-sm">
                    </a>
                @elseif($notification->type == 'follow')
                    <a href="{{ route('profile.show', $notification->from_user_id) }}" class="btn btn-outline-primary btn-sm px-3">
                        View Profile
                    </a>
                @endif
                <form action="{{ route('notifications.destroy', $notification->id) }}" method="post" class="ms-3">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn shadow-none p-0 text-secondary" title="Delete notification">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </form>
            </div>
        </div>
    @empty
        <p class="text-center mt-5">No notifications yet.</p>
    @endforelse
</div>
@endsection