@extends('layouts.app')

@section('title', 'Home')

@section('content')
<div class="row gx-5">
    <div class="col-8">
        @forelse ($home_posts as $post)
            <div class="card mb-4 shadow-sm">
                {{-- 1. ヘッダー部分（通常投稿 or リポストで分岐） --}}
                @if (isset($post->is_repost_view) && $post->is_repost_view)
                    <div class="card-header bg-white py-2 border-0">
                        {{-- リポスト表示バー --}}
                        <div class="d-flex align-items-center gap-2 mb-2 ps-1">
                            <i class="fa-solid fa-retweet text-secondary" style="font-size: 0.8rem;"></i>
                            <small class="text-muted fw-semibold">
                                @if ($post->reposter_avatar)
                                    <img src="{{ $post->reposter_avatar }}" alt="" class="rounded-circle" style="width:18px;height:18px;object-fit:cover;">
                                @else
                                    <i class="fas fa-circle-user text-secondary" style="font-size:18px;"></i>
                                @endif
                                {{ $post->reposter_name }} がリポスト
                            </small>
                        </div>
                        {{-- 元の投稿者 --}}
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <a href="{{ route('profile.show', $post->user->id) }}">
                                    @if ($post->user->avatar)
                                        <img src="{{ $post->user->avatar }}" alt="" class="rounded-circle avatar-sm">
                                    @else
                                        <i class="fas fa-circle-user text-secondary icon-sm"></i>
                                    @endif
                                </a>
                            </div>
                            <div class="col ps-0">
                                <a href="{{ route('profile.show', $post->user->id) }}" class="text-decoration-none text-dark fw-bold">{{ $post->user->name }}</a>
                            </div>
                            <div class="col-auto">
                                <div class="dropdown">
                                    <button class="btn btn-sm shadow-none" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <form action="{{ route('repost.destroy', $post->id) }}" method="post">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="fa-solid fa-retweet me-1"></i>リポストを削除
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- 通常の投稿 --}}
                    @include('users.posts.contents.title')
                @endif

                {{-- 2. 投稿内容の本体 --}}
                @include('users.posts.contents.body')
            </div>
        @empty
            <div class="text-center mt-5">
                <h2>Share Photos</h2>
                <p class="text-secondary">When you share your photos, they'll appear on your profile.</p>
                <a href="{{ route('post.create') }}" class="text-decoration-none">Share your first photo</a>
            </div>
        @endforelse
    </div>

    {{-- 右側のサイドバー --}}
    <div class="col-4">
        {{-- Profile Overview --}}
        <div class="row align-items-center mb-5 bg-white shadow-sm rounded-3 py-3">
            <div class="col-auto">
                <a href="{{ route('profile.show', Auth::user()->id) }}">
                    @if (Auth::user()->avatar)
                        <img src="{{ Auth::user()->avatar }}" alt="{{ Auth::user()->name }}" class="rounded-circle avatar-md">
                    @else
                        <i class="fa-solid fa-circle-user text-secondary icon-md"></i>
                    @endif
                </a>
            </div>
            <div class="col ps-0">
                <a href="{{ route('profile.show', Auth::user()->id) }}" class="text-decoration-none text-dark fw-bold">{{ Auth::user()->name }}</a>
                <p class="text-muted mb-0">{{ Auth::user()->email }}</p>
            </div>
        </div>

        {{-- Suggestions --}}
        @if ($suggested_users)
            <div class="row">
                <div class="col-auto">
                    <p class="fw-bold text-secondary">Suggestions For You</p>
                </div>
                <div class="col text-end">
                    <a href="{{ route('suggestions') }}" class="fw-bold text-dark text-decoration-none">See all</a>
                </div>
            </div>

            @foreach ($suggested_users as $user)
                <div class="row align-items-center mb-3">
                    <div class="col-auto">
                        <a href="{{ route('profile.show', $user->id) }}">
                            @if ($user->avatar)
                                <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="rounded-circle avatar-sm">
                            @else
                                <i class="fa-solid fa-circle-user text-secondary icon-sm"></i>
                            @endif
                        </a>
                    </div>
                    <div class="col ps-0 text-truncate">
                        <a href="{{ route('profile.show', $user->id) }}" class="text-decoration-none text-dark fw-bold">{{ $user->name }}</a>
                    </div>
                    <div class="col-auto">
                        <form action="{{ route('follow.store', $user->id) }}" method="post">
                            @csrf
                            <button type="submit" class="border-0 bg-transparent p-0 text-primary btn-sm">Follow</button>
                        </form>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>
@endsection