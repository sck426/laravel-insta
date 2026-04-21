@extends('layouts.app')

@section('title', 'Admin: Posts')

@section('content')
            <table class="table table-hover align-middle bg-white border text-secondary">
                <thead class="table-primary text-secondary small">
                    <tr>
                        <th>ID</th>
                        <th></th>
                        <th>CATEGORY</th>
                        <th>OWNER</th>
                        <th>CREATED AT</th>
                        <th>STATUS</th>
                        <th></th> 
                    </tr>
                </thead>
                <tbody>
                    @foreach ($all_posts as $post)
                    <tr>
                        <td>{{ $post->id }}</td>
                        <td>
                            <img src="{{ $post->image }}" alt="post-{{ $post->id }}" class="d-block mx-auto" style="width: 100px; height: 100px; object-fit: cover;">
                        </td>
                        <td>
                            @forelse ($post->categoryPost as $category_post)
                                <div class="badge bg-secondary bg-opacity-50 text-dark fw-light">
                                    {{ $category_post->category->name }}
                                </div>
                            @empty
                                <div class="badge bg-dark">Uncategorized</div>
                            @endforelse
                        </td>
                        <td>
                          
                            <a href="{{ route('profile.show', $post->user->id) }}" class="text-decoration-none text-dark fw-bold">
                                {{ $post->user->name }}
                            </a>
                        </td>
                        <td>{{ $post->created_at }}</td>
                        <td>
                          
                            @if ($post->trashed())
                                <i class="far fa-circle text-secondary"></i> &nbsp; Hidden
                            @else
                                <i class="fas fa-circle text-primary"></i> &nbsp; Visible
                            @endif
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm shadow-none" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis"></i>
                                </button>

                                <div class="dropdown-menu">
                                    @if ($post->trashed())
                                        <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#unhide-post-{{ $post->id }}">
                                            <i class="fas fa-eye"></i> Unhide Post
                                        </button>
                                    @else
                                        <button class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#hide-post-{{ $post->id }}">
                                            <i class="fas fa-eye-slash"></i> Hide Post
                                        </button>
                                    @endif
                                </div>
                            </div>
                            @include('admin.posts.modal.status')
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
                {{ $all_posts->links() }}
            </div>
        </div>
    </div>
</div>
@endsection