@extends('layouts.app')

@section('title', 'Following')

@section('content')
    @include('users.profile.header')

    @if ($user->following->isNotEmpty())
        <div class="row justify-content-center">
            <div class="col-4">
                <h2 class="text-muted text-center">Following</h2>

                @foreach ($user->following as $following)
                    <div class="row align-items-center mt-3">
                        <div class="col-auto">
                            <a href="{{ route('profile.show', $following->following->id) }}">
                                @if ($following->following->avatar)
                                    <img src="{{ $following->following->avatar }}" alt="{{ $following->following->name }}" class="rounded-circle avatar-sm">
                                @else
                                    <i class="fas fa-circle-user text-secondary icon-sm"></i>
                                @endif
                            </a>
                        </div>
                        <div class="col ps-0">
                            <a href="{{ route('profile.show', $following->following->id) }}" class="text-decoration-none text-dark">
                                {{ $following->following->name }}
                            </a>
                        </div>
                        <div class="col-auto text-end">
                            @if ($following->following->id != Auth::user()->id)
                                @if ($following->following->isFollowed())
                                    <form action="{{ route('follow.destroy', $following->following->id) }}" method="post">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="border-0 bg-transparent p-0 text-secondary btn-sm">Following</button>
                                    </form>
                                @else
                                    <form action="{{ route('follow.store', $following->following->id) }}" method="post">
                                        @csrf
                                        

                                        <button type="submit" class="border-0 bg-transparent p-0 text-primary btn-sm">Follow</button>
                                    </form>
                                @endif
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
@endsection