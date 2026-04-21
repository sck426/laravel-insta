{{-- clickable image --}}
<div class="container p-0 position-relative"> {{-- position-relativeを追加 --}}
    <a href="{{ route('post.show',$post->id) }}">
        <img src="{{ $post->image }}" alt="post id {{ $post->image }}" class="w-100">
    </a>

    {{-- ★ メンションボタン (人マーク) --}}
    @php
        // この投稿でメンションされているユーザーを抽出
        preg_match_all('/@([A-Za-z0-9_]+)/', $post->description, $mentionMatches);
        $mentionedNames = array_unique($mentionMatches[1]);
    @endphp

    @if(count($mentionedNames) > 0)
        <button class="btn btn-dark btn-sm position-absolute rounded-circle opacity-75" 
                style="bottom: 10px; left: 10px; width: 32px; height: 32px; padding: 0;"
                data-bs-toggle="modal" 
                data-bs-target="#mentionModal-{{ $post->id }}">
            <i class="fa-solid fa-user-tag" style="font-size: 0.8rem;"></i>
        </button>
    @endif
</div>

{{-- ★ メンション用モーダル --}}
<div class="modal fade" id="mentionModal-{{ $post->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-bold">In this photo</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <ul class="list-group list-group-flush">
                    @foreach($mentionedNames as $name)
                        @php $user = \App\Models\User::where('name', $name)->first(); @endphp
                        @if($user)
                            <a href="{{ route('profile.show', $user->id) }}" class="list-group-item list-group-item-action d-flex align-items-center border-0 py-3">
                                @if($user->avatar)
                                    <img src="{{ $user->avatar }}" class="rounded-circle me-2" style="width: 30px; height: 30px; object-fit: cover;">
                                @else
                                    <i class="fa-solid fa-circle-user text-secondary me-2" style="font-size: 30px;"></i>
                                @endif
                                <span class="fw-bold small">{{ $user->name }}</span>
                            </a>
                        @endif
                    @endforeach
                </ul>
            </div>
            <div class="pb-2"></div>
        </div>
    </div>
</div>


<div class="card-body">
    {{-- heart button + no. of likes + categories --}}
    <div class="row align-items-center">
        <div class="col-auto">
           @if ($post->isliked())
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

        <div class="col-auto">
            @if ($post->isReposted())
                <form action="{{ route('repost.destroy', $post->id) }}" method="post">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn shadow-none p-0" title="リポスト済み">
                        <i class="fa-solid fa-retweet text-primary" style="font-size: 1.2rem;"></i>
                    </button>
                </form>
            @else
                <form action="{{ route('repost.store', $post->id) }}" method="post">
                    @csrf
                    <button type="submit" class="btn shadow-none p-0" title="リポスト">
                        <i class="fa-solid fa-retweet text-secondary" style="font-size: 1.2rem;"></i>
                    </button>
                </form>
            @endif
        </div>
        <div class="col-auto px-0">
            <span>{{ $post->reposts->count() }}</span>
        </div>
        
        <div class="col text-end">   
            @forelse ($post->categoryPost as $category_post)
                 <span class="badge bg-secondary bg-opacity-50">{{ $category_post->category->name }}</span>
            @empty
                 <span class="badge bg-dark">Uncategorized</span>
            @endforelse     
        </div>
    </div>

    {{-- owner + description (メンションリンク付き) --}}
    <div class="mt-2">
        <a href="{{ route('profile.show',$post->user->id) }}"
           class="text-decoration-none text-dark fw-bold">{{ $post->user->name }}</a>
        &nbsp;

        <p class="d-inline fw-light">
            {!! 
                // 2. ハッシュタグ (#) をリンク化
                // あなたのルート設定: /tag/{name} に合わせて修正
                preg_replace(
                    '/#([A-Za-z0-9_]+)/',
                    '<a href="'.url('/tag').'/$1" class="text-decoration-none text-primary">#$1</a>',

                    // 1. メンション (@) をリンク化
                    // プロフィールがID指定のため、名前からIDを取得する処理はControllerで行うのが理想ですが、
                    // ここでは簡易的に、受け側のProfileControllerを後で修正する前提でリンクを作ります
                    preg_replace(
                        '/@([A-Za-z0-9_]+)/', 
                        '<a href="'.url('/profile').'/$1/show" class="text-decoration-none fw-bold">@$1</a>', 
                        e($post->description)
                    )
                )
            !!}
        </p>
            </div>

    <p class="text-uppercase text-muted xsmall mt-1">{{ date('M d, Y', strtotime($post->created_at)) }}</p>

    {{-- include comments here --}}
    @include('users.posts.contents.comments')
</div>