<div class="row align-items-end">
    {{-- アイコン --}}
    <div class="col-auto" style="position: relative; z-index: 10;">
        @if ($user->avatar)
            <img src="{{ $user->avatar }}" alt="{{ $user->name }}"
                 class="rounded-circle border border-5 border-white shadow-sm bg-white"
                 style="width: 150px; height: 150px; object-fit: cover;">
        @else
            <div class="rounded-circle border border-5 border-white shadow-sm bg-white d-flex align-items-center justify-content-center"
                 style="width: 150px; height: 150px;">
                <i class="fas fa-circle-user text-secondary" style="font-size: 138px; line-height: 1;"></i>
            </div>
        @endif
    </div>

    {{-- 情報エリア --}}
    <div class="col pb-2">
        <div class="d-flex align-items-center mb-2">
            <h2 class="display-6 mb-0 fw-bold me-3">{{ $user->name }}</h2>

            @if (Auth::user()->id === $user->id)
                <a href="{{ route('profile.edit', Auth::id()) }}" class="btn btn-outline-secondary btn-sm fw-bold px-3 rounded-pill">Edit Profile</a>
            @else
                @if ($user->isFollowed())
                    <form action="{{ route('follow.destroy',$user->id) }}" method="post" class="d-inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-outline-secondary btn-sm fw-bold px-3 rounded-pill">Following</button>
                    </form>
                @else
                    <form action="{{ route('follow.store', $user->id) }}" method="post" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm fw-bold px-4 rounded-pill">Follow</button>
                    </form>
                @endif
            @endif
        </div>

        <div class="d-flex gap-3 mb-2">
            @php $postCount = isset($posts) ? $posts->count() : $user->posts()->whereNull('deleted_at')->where('status', '!=', 'archived')->count(); @endphp
            <span class="text-dark"><strong>{{ $postCount }}</strong> {{ \Illuminate\Support\Str::plural('post', $postCount) }}</span>
            <a href="{{ route('profile.followers',$user->id) }}" class="text-decoration-none text-dark">
                <strong>{{ $user->followers()->count() }}</strong> {{ \Illuminate\Support\Str::plural('follower', $user->followers()->count()) }}
            </a>
            <a href="{{ route('profile.following',$user->id) }}" class="text-decoration-none text-dark">
                <strong>{{ $user->following->count() }}</strong> following
            </a>
        </div>

        <p class="fw-bold mb-0">{{ $user->introduction }}</p>
    </div>
</div>