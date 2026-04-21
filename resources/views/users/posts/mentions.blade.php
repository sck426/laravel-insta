@extends('layouts.app')

@section('title', 'Mentioned Posts')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-8">
            <h3 class="fw-bold mb-4">Posts mentioning you</h3>

            {{-- $mentions を $all_posts に書き換え --}}
            @forelse($all_posts as $post)
                <div class="card mb-4">
                    {{-- $post をそのまま渡す --}}
                    @include('users.posts.contents.title', ['post' => $post])
                    @include('users.posts.contents.body', ['post' => $post])
                </div>
            @empty
                <div class="text-center mt-5">
                    <i class="fa-solid fa-at fa-3x text-secondary mb-3"></i>
                    <h2 class="text-muted">No Mentions Yet</h2>
                    <p class="text-secondary">When someone mentions you, the post will appear here.</p>
                </div>
            @endforelse

            {{-- ページネーション --}}
            <div class="d-flex justify-content-center">
                {{ $all_posts->links() }}
            </div>
        </div>
    </div>
</div>
@endsection