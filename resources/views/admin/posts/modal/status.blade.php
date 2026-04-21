{{-- Hide Post --}}
<div class="modal fade" id="hide-post-{{ $post->id }}">
    <div class="modal-dialog">
        <div class="modal-content border-danger">
            <div class="modal-header border-danger">
                <h3 class="modal-title text-danger">
                    <i class="fas fa-eye-slash"></i> Hide Post
                </h3>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to hide this post?</p>
                <div class="mt-3">
                    <img src="{{ $post->image }}" alt="post-{{ $post->id }}" class="image-lg w-100 shadow-sm">
                    <p class="mt-1 text-muted">{{ $post->description }}</p>
                </div>
            </div>
            <div class="modal-footer border-0">
                <form action="{{ route('admin.posts.hide', $post->id) }}" method="post">
                    @csrf
                    @method('DELETE')

                    <button class="btn btn-outline-danger btn-sm" data-bs-dismiss="modal" type="button">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm">Hide</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Unhide Post --}}
<div class="modal fade" id="unhide-post-{{ $post->id }}">
    <div class="modal-dialog">
        <div class="modal-content border-primary">
            <div class="modal-header border-primary">
                <h3 class="modal-title text-primary">
                    <i class="fas fa-eye"></i> Unhide Post
                </h3>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to unhide this post?</p>
                <div class="mt-3">
                    <img src="{{ $post->image }}" alt="post-{{ $post->id }}" class="image-lg w-100 shadow-sm">
                    <p class="mt-1 text-muted">{{ $post->description }}</p>
                </div>
            </div>
            <div class="modal-footer border-0">
                <form action="{{ route('admin.posts.unhide', $post->id) }}" method="post">
                    @csrf
                    @method('PATCH')

                    <button class="btn btn-outline-primary btn-sm" data-bs-dismiss="modal" type="button">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Unhide</button>
                </form>
            </div>
        </div>
    </div>
</div>