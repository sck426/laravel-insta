@extends('layouts.app')

@section('title','Category')

@section('content')
 <div class="row gx-5">
    <div class="col-8">
        {{-- change here --}}
        @forelse($posts as $post)
            <div class="card mb-4">
                @include('users.posts.contents.title')
                @include('users.posts.contents.body')
            </div>
        @empty
           {{-- if the site doesn't have any posts yet. --}}
            <div class="text-center">
                <h2>Share Photos</h2>
                <p class="text-secondary">No Posts Found. </p>
                <a href="{{ route('post.create') }}" class="text-decoration-none">Share your photo</a>
            </div>
        @endforelse
       
    </div>

    <div class="col-4">
        {{-- Profile overview --}}
        <div class="row align-items-center mb-5 bg-white shadow-sm rounded py-3">
            <div class="col-auto">
                <a href="{{ route('profile.show',Auth::user()->id) }}">
                    @if (Auth::user()->avatar)
                       <img src="{{ Auth::user()->avatar }}" alt="{{ Auth::user()->name }}" class="rounded-circle avatar-md">
                    @else
                          <i class="fas fa-circle-user text-secondary icon-md"></i>
                    @endif
                </a>
            </div>

            <div class="col ps-0">
                <a href="{{ route('profile.show',Auth::user()->id) }}" class="text-decoration-none text-dark fw-bold">{{ Auth::user()->name }}</a>
                <p class="text-muted mb-0">{{ Auth::user()->email }}</p>
            </div>
        </div>
    </div>
 </div>
@endsection
