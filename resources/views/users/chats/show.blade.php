@extends('layouts.app')

@section('title','Chat')
    
@section('content')
    
    <div class="card text-center w-50 mx-auto">
        <div class="card-header">
            <a href="{{ route('profile.show',$partner->id) }}" class="text-decoration-none text-dark">
                @if ($partner->avatar)
                    <img src="{{ $partner->avatar }}" alt="{{ $partner->name }}" class="rounded-circle avatar-md">
                    <h4>{{ $partner->name }}</h4>
                @else
                    <i class="fas fa-circle-user text-secondary icon-md"></i>
                    <h4>{{ $partner->name }}</h4>
                @endif  
            </a>
        </div>
        
        <div class="card-body chat-body" style="height: 450px">
            @forelse ($messages as $message)
                <div class="msg-row {{ $message->user_id == Auth::id() ? 'my-message' : 'other-message' }}">
                    <div class="msg">
                        <div class="msg-meta">{{ $message->user->name }}</div>
                        <div class="bubble">{{ $message->body }}</div>
                    </div>
                </div>  
            @empty
                 <h3 class="text-muted pt-5">You can start chatting here.</h3>
            @endforelse
                  
        </div>
            
        <div class="card-footer text-body-secondary">

            <form action="{{ route('message.store', $chat_room->id) }}" method="POST">
                @csrf
                <input type="text" name="body" class="w-75" required>
                <button type="submit" class="btn btn-outline-dark btn-sm"><i class="fa-regular fa-paper-plane"></i></button>
            </form>
            
        </div>
    </div>

    
@endsection