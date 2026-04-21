@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h4 class="mb-4 fw-bold">User List</h4>
            
            <div class="card shadow-sm">
                <div class="list-group list-group-flush">
                    @foreach($all_users as $user)
                        {{-- 自分自身は表示しない --}}
                        @if($user->id !== Auth::id())
                            <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <div class="d-flex align-items-center">
                                    {{-- アイコンの表示 --}}
                                    <div class="position-relative me-3">
                                    @if($user->avatar)
                                        <img src="{{ $user->avatar }}" class="rounded-circle" style="width: 45px; height: 45px; object-fit: cover;">
                                    @else
                                        <i class="fa-solid fa-circle-user text-secondary" style="font-size: 45px;"></i>
                                    @endif

                                    {{-- ★ここからバッジの表示 --}}
                                    @php
                                        $unreadFromUser = Auth::user()->unreadMessagesFrom($user->id);
                                    @endphp

                                    @if($unreadFromUser > 0)
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" 
                                            style="font-size: 0.6rem; padding: 0.25rem 0.4rem;">
                                            {{ $unreadFromUser > 9 ? '9+' : $unreadFromUser }}
                                            <span class="visually-hidden">unread messages</span>
                                        </span>
                                    @endif
                                    </div>
                                                                
                                    <span class="fw-bold">{{ $user->name }}</span>
                                </div>

                                {{-- このボタンを押すと ChatRoomController@store が動き、部屋が作られる --}}
                                <form action="{{ route('chat.store', $user->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm px-4">Chat</button>
                                </form>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection