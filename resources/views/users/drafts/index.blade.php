@extends('layouts.app')

@section('title', 'Drafts')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-8">
                <h2 class="h4 text-muted mb-4">
                    <i class="fas fa-file-pen me-2"></i>Drafts
                </h2>

                {{-- 下書きがあれば表示 --}}
                {{-- 変数名は後でコントローラーで $drafts として渡す想定です --}}
                @if(isset($drafts) && $drafts->isNotEmpty())
                    <div class="list-group">
                        @foreach($drafts as $post)
                            <a href="{{ route('post.edit', $post->id) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
                                <div class="d-flex align-items-center">
                                    {{-- 画像があればサムネイル表示 --}}
                                    @if($post->image)
                                        <img src="{{ $post->image }}" alt="draft" class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                    @else
                                        <div class="bg-secondary rounded me-3 d-flex align-items-center justify-content-center text-white" style="width: 50px; height: 50px;">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    @endif

                                    <div>
                                         {{-- $post->categoryPost gets all the categories of a post  --}}
                                            @forelse ($post->categoryPost as $category_post)
                                             <span class="badge bg-secondary bg-opacity-50">{{ $category_post->category->name }} </span>
                                            @empty
                                             <span class="badge bg-dark">Uncategorized</span>
                                            @endforelse
                                            <p class="mb-0 text-muted small text-truncate" style="max-width: 300px;">
                                            {{ $post->description ?? 'No description' }}
                                        </p>
                                    </div>
                                </div>
                                
                                <span> <i class="fas fa-chevron-right ms-1"></i></span>
                            </a>
                        @endforeach
                    </div>
                @else
                    {{-- 下書きがない場合 --}}
                    <div class="text-center mt-5">
                        <p class="lead text-muted">No drafts found.</p>
                        <a href="{{ route('post.create') }}" class="btn btn-outline-primary btn-sm">Create a new post</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection