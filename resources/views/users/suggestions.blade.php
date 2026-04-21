@extends('layouts.app')

@section('title', 'Recommendations')

@section('content')
<div class="row justify-content-center">
    <div class="col-6">
        {{-- タイトル部分 --}}
        <h4 class="h5 text-secondary fw-bold mb-4">Suggested Users For You</h4>

        <div class="bg-white shadow-sm rounded-3 p-4">
            @forelse ($suggested_users as $user)
                <div class="row align-items-center mb-3">
                    {{-- アバター部分 --}}
                    <div class="col-auto">
                        <a href="{{ route('profile.show', $user->id) }}">
                            @if ($user->avatar)
                                <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="rounded-circle avatar-sm">
                            @else
                                <i class="fas fa-circle-user text-secondary icon-sm"></i>
                            @endif
                        </a>
                    </div>

                    {{-- ユーザー情報（名前とメールアドレス） --}}
                    <div class="col ps-0 text-truncate">
                        <a href="{{ route('profile.show', $user->id) }}" class="text-decoration-none text-dark fw-bold">
                            {{ $user->name }}
                        </a>
                        <p class="text-muted mb-0 small">{{ $user->email }}</p>
                    </div>

                    {{-- フォローボタン --}}
                    <div class="col-auto">
                        <form action="{{ route('follow.store', $user->id) }}" method="post">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-sm px-3 fw-bold">Follow</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <p class="text-muted">No recommendations found at the moment.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection