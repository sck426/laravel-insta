@extends('layouts.app')

@section('title','Show Post')

@section('content')
<style>
    .col-4{
        /* スクロールバー*/
        overflow-y: scroll;
    }

    .card-body{
        position: absolute;
        top: 65px;
    }
</style>
   <div class="row border shadow">
        {{-- 左側：画像エリア --}}
        <div class="col p-0 border-end position-relative"> {{-- position-relativeを追加 --}}
            <img src="{{ $post->image }}" alt="Post id {{ $post->id }}" class="w-100">

            {{-- ★ メンションボタン (左下) --}}
            @php
                preg_match_all('/@([A-Za-z0-9_]+)/', $post->description, $mentionMatches);
                $mentionedNames = array_unique($mentionMatches[1]);
            @endphp

            @if(count($mentionedNames) > 0)
                <button class="btn btn-dark btn-sm position-absolute rounded-circle opacity-75" 
                        style="bottom: 15px; left: 15px; width: 38px; height: 38px; padding: 0; z-index: 10;"
                        data-bs-toggle="modal" 
                        data-bs-target="#mentionModal-{{ $post->id }}">
                    <i class="fa-solid fa-user-tag" style="font-size: 0.9rem;"></i>
                </button>
            @endif
        </div>

        {{-- 右側：詳細・コメントエリア --}}
        <div class="col-4 px-0 bg-white">
            <div class="card border-0">
                <div class="card-header bg-white py-3">
                    <div class="row align-items-center">
                       <div class="col-auto">
                            <a href="{{ route('profile.show',$post->user->id) }}">
                                @if ($post->user->avatar)
                                  <img src="{{ $post->user->avatar }}" alt="{{ $post->user->name }}" class="rounded-circle avatar-sm">     
                                @else
                                  <i class="fas fa-solid fa-circle-user text-secondary icon-sm"></i>
                                @endif
                            </a>
                        </div>
                        <div class="col ps-0">
                            <a href="{{ route('profile.show',$post->user->id) }}" class="text-decoration-none text-dark">{{ $post->user->name }}</a>
                        </div>
                        <div class="col-auto">
                            @if (Auth::user()->id === $post->user->id)
                                <div class="dropdown">
                                    <button class="btn btn-sm shadow-none" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis"></i>
                                    </button>

                                    <div class="dropdown-menu">
                                        <a href="{{ route('post.edit',$post->id) }}" class="dropdown-item text-dark">
                                            <i class="far fa-pen-to-square"></i> Edit
                                        </a>
                                        <form action="{{ route('post.destroy',$post->id) }}" method="post">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="far fa-trash-can"></i> Delete
                                            </button>
                                        </form>
                                        @if($post->trashed())
                                            <form action="{{ route('post.restore', $post->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="dropdown-item">
                                                    <i class="fa-solid fa-clock-rotate-left"></i> UnArchive
                                                </button>
                                            </form>
                                        @else
                                           <form action="{{ route('post.archive_exec', $post->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="dropdown-item">
                                                    <i class="fa-solid fa-clock-rotate-left"></i> Archive
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                    @include('users.posts.contents.modals.delete')
                                </div>
                            @else
                                @if($post->user->isFollowed())
                                    <form action="{{ route('follow.destroy',$post->user->id) }}" method="post">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="border-0 bg-transparent p-0 text-secondary">Following</button>
                                    </form>
                                @else
                                    <form action="{{ route('follow.store', $post->user->id) }}" method="post">
                                        @csrf
                                        <button type="submit" class="border-0 bg-transparent p-0 text-primary">Follow</button>
                                    </form>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card-body w-100">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            @if ($post->isLiked())
                               <form action="{{ route('like.destroy',$post->id) }}" method="post">
                                @csrf
                                @method('DELETE')
                                  <button type="submit" class="btn btn-shadow-none p-0">
                                    <i class="fas fa-heart text-danger"></i>
                                  </button>
                               </form>
                            @else
                                <form action="{{ route('like.store',$post->id) }}" method="post">
                                    @csrf
                                    <button type="submit" class="btn btn-shadow-none p-0">
                                        <i class="far fa-heart"></i>
                                    </button>
                                </form>
                             @endif
                        </div>
                        <div class="col-auto px-0">
                            <span>{{ $post->likes->count() }}</span>
                        </div>
                        <div class="col text-end">
                            @forelse ($post->categoryPost as $category_post)
                            <span class="badge bg-secondary bg-opacity-50">{{ $category_post->category->name }} </span>
                            @empty
                            <span class="badge bg-dark">Uncategorized</span>
                            @endforelse
                        </div>
                    </div>

                    <a href="{{ route('profile.show',$post->user->id) }}" class="text-decoration-none text-dark fw-bold">{{ $post->user->name }}</a>
                    &nbsp;
                    
                    {{-- ★ 本文のリンク化 (Mention & Hashtag) --}}
                    @php
                        $desc = e($post->description);
                        // Hashtag
                        $desc = preg_replace_callback('/#([A-Za-z0-9_]+)/', function($m){
                            return '<a href="'.route('tags.show', strtolower($m[1])).'" class="text-primary text-decoration-none">#'.strtolower($m[1]).'</a>';
                        }, $desc);
                        // Mention
                        $desc = preg_replace_callback('/@([A-Za-z0-9_]+)/', function($m){
                            $u = \App\Models\User::where('name', $m[1])->first();
                            return $u ? '<a href="'.route('profile.show', $u->id).'" class="text-primary fw-bold text-decoration-none">@'.$m[1].'</a>' : '@'.$m[1];
                        }, $desc);
                    @endphp
                    <p class="d-inline fw-light">{!! $desc !!}</p>
                    
                    <p class="text-uppercase text-muted xsmall">{{ date('M d, Y', strtotime($post->created_at)) }}</p>

                    {{-- コメントフォーム --}}
                     <form action="{{ route('comment.store', $post->id) }}" method="post" class="mb-3">
                        @csrf
                        <div class="input-group">
                            <textarea name="comment_body{{ $post->id }}" cols="30" rows="1" class="form-control form-control-sm" placeholder="Add a comment...">{{ old('comment_body' . $post->id) }}</textarea>
                            <button type="submit" class="btn btn-outline-secondary btn-sm"><i class="far fa-paper-plane"></i></button>
                        </div>
                        @error('comment_body' . $post->id)
                            <p class="text-danger small">{{ $message }}</p>
                        @enderror
                    </form>

                    {{-- コメント一覧 --}}
                     @if ($post->comments->isNotEmpty())
                      <ul class="list-group mt-2">
                         @foreach ($post->comments as $comment)
                           <li class="list-group-item border-0 p-0 mb-2">
                            <a href="{{ route('profile.show',$comment->user->id) }}" class="text-decoration-none text-dark fw-bold">{{ $comment->user->name }}</a>
                            &nbsp;
                            <p class="d-inline fw-light">{{ $comment->body }}</p>
                            <form action="{{ route('comment.destroy',$comment->id) }}" method="post">
                                @csrf
                                @method('DELETE')
                                <span class="text-uppercase text-muted xsmall">{{ date('M d, Y', strtotime($comment->created_at)) }}</span>
                                @if (Auth::id() == $comment->user->id)
                                &middot; <button type="submit" class="border-0 bg-transparent text-danger p-0 xsmall">Delete</button>                                
                                @endif
                            </form>
                           </li>
                         @endforeach
                      </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ★ メンション用モーダル --}}
    @if(count($mentionedNames) > 0)
    <div class="modal fade" id="mentionModal-{{ $post->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h6 class="modal-title fw-bold">Tagged Users</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($mentionedNames as $name)
                            @php $u = \App\Models\User::where('name', $name)->first(); @endphp
                            @if($u)
                                <a href="{{ route('profile.show', $u->id) }}" class="list-group-item list-group-item-action border-0 d-flex align-items-center">
                                    @if($u->avatar)
                                        <img src="{{ $u->avatar }}" class="rounded-circle me-2" style="width: 30px; height: 30px; object-fit: cover;">
                                    @else
                                        <i class="fa-solid fa-circle-user text-secondary me-2" style="font-size: 30px;"></i>
                                    @endif
                                    <span class="fw-bold small">{{ $u->name }}</span>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    
@endsection