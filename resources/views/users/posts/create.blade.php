{{-- resources/views/posts/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Create Post')

@section('content')
    <form action="{{ route('post.store') }}" method="post" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="category" class="form-label d-block fw-bold">
                Category <span class="text-muted fw-bold">(up to 3)</span>
            </label>

            @foreach ($all_categories as $category)
                <div class="form-check form-check-inline">
                    <input type="checkbox"
                           name="category[]"
                           id="{{ $category->name }}"
                           value="{{ $category->id }}"
                           class="form-check-input">
                    <label for="{{ $category->name }}" class="form-check-label">
                        {{ $category->name }}
                    </label>
                </div>
            @endforeach

            {{-- Error --}}
        </div>

        <div class="mb-3">
            <label for="description" class="form-label fw-bold">Description</label>
            <textarea name="description"
                      id="description"
                      rows="3"
                      class="form-control"
                      placeholder="What's on your mind">{{ old('description') }}</textarea>
            {{-- Error --}}
        </div>

        <div class="mb-3">
            <label for="image" class="form-label fw-bold">Photo</label>
            <input type="file"
                   name="image"
                   id="image"
                   class="form-control"
                   aria-describedby="image-info">

            <div id="image-info" class="form-text">
                Acceptable formats are jpeg, jpg, png, and gif only. <br>
                Max file size is 1048kb.
            </div>
            {{-- Error --}}
            @error('image')
              <p class="text-danger small">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" name="action" value="published" class="btn btn-warning btn-sm">
        Post
        </button>

        <button type="submit" name="action" value="draft" class="btn btn-primary btn-sm">
        Draft
        </button>
    </form>
@endsection