@extends('layouts.app')

@section('title','Edit Post')

@section('content')
<form action="{{ route('post.update', $post->id) }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('PATCH')

        <div class="mb-3">
            <label for="category" class="form-label d-block fw-bold">
                Category <span class="text-muted fw-bold">(up to 3)</span>
            </label>

            @foreach ($all_categories as $category)
            <div class="form-check form-check-inline">
            @if (in_array($category->id, $selected_categories))  
                <input type="checkbox" name="category[]" id="{{ $category->name }}" value="{{ $category->id }}" class="form-check-input" checked>
            @else    
            <input type="checkbox"
                           name="category[]"
                           id="{{ $category->name }}"
                           value="{{ $category->id }}"
                           class="form-check-input">
            @endif    
            <label for="{{ $category->name }}" class="form-check-label">{{ $category->name }}</label>
            </div>
            @endforeach
            {{-- Error --}}
            @error('category')
                <p class="text-danger small">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-3">
            <label for="description" class="form-label fw-bold">Description</label>
            <textarea name="description"
                      id="description"
                      rows="3"
                      class="form-control"
                      placeholder="What's on your mind">{{ old('description', $post->description) }}</textarea>
            {{-- Error --}}
        </div>

        <div class="row mb-3">
            <label for="image" class="form-label fw-bold">Photo</label>
            <img src="{{ $post->image }}" alt="post id {{ $post->id }}" class="img-thumbnail w-100">
            <input type="file"
                   value="{{ old('image', $post->image) }}"
                   name="image"
                   id="image"
                   class="form-control mt-1"
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

        @if ($post->status == 'published')
        <button type="submit" name="action" value="published" class="btn btn-warning btn-sm">Save</button>
        @else
        <button type="submit" name="action" value="published" class="btn btn-warning btn-sm">Post</button>
        @endif

        @if ($post->status == 'draft')
            <button type="submit" name="action" value="draft" class="btn btn-outline-warning">Save as Draft</button> 
        @endif
            </form>

        @endsection