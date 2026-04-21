<div class="card-header bg-white py-3">
    <div class="row align-items-center">
        <div class="col-auto">
            <a href="{{ route('profile.show', $post->user->id) }}">
                @if ($post->user->avatar)
                   <img src="{{ $post->user->avatar }}" alt="{{ $post->user->name }}" class="rounded-circle avatar-sm">
                @else
                   <i class="fas fa-circle-user text-secondary icon-sm"></i>
                @endif
            </a>
        </div>
        <div class="col ps-0">
             <a href="{{ route('profile.show', $post->user->id) }}" class="text-decoration-none text-dark">{{ $post->user->name }}</a>
        </div>
        <div class="col-auto">
            <div class="dropdown">
                <button class="btn btn-sm shadow-none" data-bs-toggle=dropdown>
                 <i class="fas fa-ellipsis"></i>
                </button>

                {{-- if you are the owner of the post, show EDIT and DELETE --}}
                @if (Auth::user()->id === $post->user->id)
                <div class="dropdown-menu">
                    <a href="{{ route('post.edit', $post->id) }}" class="dropdown-item">
                        <i class="far fa-pen-to-square"></i>Edit
                    </a>

                    {{-- アーカイブボタン（公開中のみ表示） --}}
                @if($post->status !== 'archived')
                    <form action="{{ route('post.archive_exec', $post->id) }}" method="post">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="dropdown-item text-dark">
                            <i class="fa-solid fa-clock-rotate-left"></i> Archive
                        </button>
                    </form>
                @endif

                    <button class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#delete-post-{{ $post->id }}"><i class="far fa-trash-can"></i>Delete
                    </button>
                </div>
                @include('users.posts.contents.modals.delete')
                @else
                {{-- if NOT the owner of the post, show UNFOLLOW button --}}
                <div class="dropdown-menu">
                    <form action="{{ route('follow.destroy', $post->user->id) }}" method="post">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="dropdown-item text-danger">Unfollow</button>
                    </form>

                        <form action="{{ route('chat.store', $post->user->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item text-decoration-none text-dark">
                                Chat <i class="fa-regular fa-paper-plane"></i>
                            </button>
                        </form>

                </div>
                @endif
            </div>
        </div>
    </div>
</div>