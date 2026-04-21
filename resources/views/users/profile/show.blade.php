@extends('layouts.app')

@section('title', 'Profile')

@section('content')

     @if (session('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
               {{ session('success') }}
               <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
     @endif
     <div class="profile-cover-wrapper w-100" style="height: 300px; background-color: #f0f2f5; overflow: hidden; position: relative;">
          @if($user->cover_photo)
               <img src="{{ $user->cover_photo }}" class="w-100 h-100" style="object-fit: cover;">
          @else
               <div class="w-100 h-100 bg-secondary-subtle"></div> {{-- 画像がない時の背景色 --}}
          @endif
     </div>

     <div class="container position-relative" style="margin-top: -75px; z-index: 5;">
           @include('users.profile.header')
     </div>

     {{-- タブ --}}
     <div style="margin-top:100px">
          <ul class="nav nav-tabs justify-content-center border-top mb-4">
               <li class="nav-item">
                    <a class="nav-link text-secondary fw-semibold active" id="posts-tab" data-bs-toggle="tab" href="#posts" role="tab">
                         <i class="fa-solid fa-grid-2 me-1"></i> Posts
                    </a>
               </li>
               <li class="nav-item">
                    <a class="nav-link text-secondary fw-semibold" id="reposts-tab" data-bs-toggle="tab" href="#reposts" role="tab">
                         <i class="fa-solid fa-retweet me-1"></i> Reposts
                    </a>
               </li>
          </ul>

          <div class="tab-content">
               {{-- 投稿タブ --}}
               <div class="tab-pane fade show active" id="posts" role="tabpanel">
                    @if ($posts->isNotEmpty())
                         <div class="row">
                              @foreach ($posts as $post)
                              <div class="col-lg-4 col-md-6 mb-4">
                                   <a href="{{ route('post.show', $post->id) }}">
                                        <img src="{{ $post->image }}" alt="post id {{ $post->id }}" class="grid-img">
                                   </a>
                              </div>
                              @endforeach
                         </div>
                    @else
                         <h3 class="text-muted text-center">No Posts Yet.</h3>
                    @endif
               </div>

               {{-- リポストタブ --}}
               <div class="tab-pane fade" id="reposts" role="tabpanel">
                    @if ($reposted_posts->isNotEmpty())
                         <div class="row">
                              @foreach ($reposted_posts as $post)
                              <div class="col-lg-4 col-md-6 mb-4 position-relative">
                                   <a href="{{ route('post.show', $post->id) }}">
                                        <img src="{{ $post->image }}" alt="post id {{ $post->id }}" class="grid-img">
                                   </a>
                                   {{-- リポストアイコンバッジ --}}
                                   <span class="position-absolute top-0 end-0 m-2 bg-white rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                                         style="width:28px;height:28px;">
                                        <i class="fa-solid fa-retweet text-primary" style="font-size:0.75rem;"></i>
                                   </span>
                              </div>
                              @endforeach
                         </div>
                    @else
                         <h3 class="text-muted text-center">No Reposts Yet.</h3>
                    @endif
               </div>
          </div>
     </div>

     
@endsection