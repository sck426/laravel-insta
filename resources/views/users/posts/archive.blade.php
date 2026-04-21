@extends('layouts.app')

@section('title', 'Archive Posts')

@section('content')
    <div class="container">
        <h2 class="h4 mb-4">
            <i class="fa-solid fa-clock-rotate-left"></i> Archived Posts
        </h2>
        <div class="row">
            {{-- 変数名はPostControllerのarchiveIndexで渡しているものに合わせてください --}}
            @forelse($archived_posts as $post)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card shadow-sm border">
                        <a href="{{ route('post.show', $post->id) }}">
                            @if($post->image)
                                <img src="{{ $post->image }}" alt="post image" class="card-img-top" style="height: 250px; object-fit: cover;">
                            @else
                                <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" style="height: 250px;">
                                    <i class="fa-solid fa-image fa-3x text-white"></i>
                                </div>
                            @endif
                        </a>
                        <div class="card-body bg-white">
                            <p class="card-text text-truncate">
                                <strong>{{ $post->user->name }}</strong> {{ $post->description }}
                            </p>
                        </div>
                        
                        {{-- ★ ここにアンアーカイブボタン --}}
                        <div class="card-footer text-end border-0 bg-white pt-0 pb-3">
                            <form action="{{ route('post.unarchive', $post->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center mt-5">
                    <p class="text-muted">You haven't archived any posts yet.</p>
                    <a href="{{ route('index') }}" class="btn btn-outline-primary btn-sm">Explore Posts</a>
                </div>
            @endforelse
        </div>
    </div>
@endsection