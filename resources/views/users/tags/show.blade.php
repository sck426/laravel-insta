@extends('layouts.app')

@section('title', '#' . $name)

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-9">
            <h2 class="h4 mb-4">
                <i class="fa-solid fa-hashtag text-primary"></i> {{ $name }}
                <span class="text-muted small">({{ $posts->count() }} posts)</span>
            </h2>

            <div class="row">
                @forelse($posts as $post)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card shadow-sm border">
                            <a href="{{ route('post.show', $post->id) }}">
                                @if($post->image)
                                    <img src="{{ $post->image }}" class="card-img-top" style="height: 250px; object-fit: cover;">
                                @else
                                    <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" style="height: 250px;">
                                        <i class="fa-solid fa-image fa-3x text-white"></i>
                                    </div>
                                @endif
                            </a>
                            <div class="card-body bg-white">
                                <a href="{{ route('profile.show',$post->user->id) }}"
                                class="text-decoration-none text-dark fw-bold">{{ $post->user->name }}</a>
                                &nbsp;

                                @php
                                $text = e($post->description);

                                // #tag をリンクに変換（ルールA: 英数字＋_）
                                $text = preg_replace_callback('/#([A-Za-z0-9_]+)/', function ($m) {
                                    $name = strtolower($m[1]);          // 小文字に統一（好みで外してOK）
                                    $url  = route('tags.show', $name);
                                    return '<a href="'.$url.'" class="text-primary me-1 text-decoration-none">#'.$name.'</a>';
                                }, $text);
                                @endphp

                                <p class="d-inline fw-light">{!! $text !!}</p>

                                <p class="text-uppercase text-muted xsmall">{{ date('M d, Y', strtotime($post->created_at)) }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center mt-5">
                        <p class="text-muted">No posts found with this hashtag.</p>
                        <a href="{{ route('index') }}" class="btn btn-outline-primary btn-sm">Go Home</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection