@extends('layouts.app')

@section('title', 'Recommend Posts')

@section('content')
<div class="container"> {{-- 全体を囲むコンテナを追加すると安定します --}}
    <div class="row justify-content-center"> {{-- 中央寄せにするなら row を --}}
        <div class="col-8">
            @forelse($recommend_posts as $post)
                <div class="card mb-4">
                    @include('users.posts.contents.title')
                    @include('users.posts.contents.body')
                </div>
            @empty
                <div class="text-center">
                    <h2>No Posts Yet</h2>
                    <p class="text-secondary">When you share photos, they'll appear on your profile.</p>
                    <a href="{{ route('post.create') }}" class="text-decoration-none">Share your first photo</a>
                </div>
            @endforelse
        </div> {{-- col-8 の閉じ --}}
    </div> {{-- row の閉じ --}}
</div> {{-- container の閉じ --}}
@endsection