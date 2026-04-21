@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
     <div class="row justify-content-center">
        <div class="col-8">
            <form action="{{ route('profile.update', $user->id) }}" method="post" class="bg-white shadow rounded-3 p-5" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            
            <h2 class="h3 mb-3 fw-light text-muted">Update Profile</h2>

            {{-- Cover Photo Section --}}
            <div class="mb-4 pt-3 border-top">
                <label for="cover_photo" class="form-label fw-bold">Cover Photo</label>
                
                @if ($user->cover_photo)
                    <div class="mb-2">
                        <img src="{{ $user->cover_photo }}" class="img-thumbnail w-100" style="height: 120px; object-fit: cover;">
                    </div>
                @endif
                
                <input type="file" name="cover_photo" id="cover_photo" class="form-control form-control-sm">
                <div class="form-text">
                    Acceptable formats: jpeg, jpg, png, gif only. <br>
                    Recommended size: 1500 x 500 pixels.
                </div>

                @error('cover_photo')
                    <p class="text-danger small mb-0">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="row mb-3">
                <div class="col-4">
                    @if ($user->avatar)
                      <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="img-thumbnail rounded-circle d-block mx-auto">
                    @else
                      <i class="fas fa-circle-user text-secondary d-block mx-auto text-center icon-lg"></i>
                    @endif                   
                </div>
                <div class="col-auto align-self-end">
                    <input type="file" name="avatar" id="avatar" class="form-control form-control-sm mt-1">
                    <div id="avatar-info" class="form-text">
                        Acceptable formats: jpeg, jpg, png, gif only. <br>
                        Max file size is 1048kb.
                    </div>
                    {{-- Error --}}
                    @error('avatar')
                    <p class="text-danger small mb-0">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div class="mb-3">
                <label for="name" class="form-label fw-bold">Name</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}" autofocus>
                {{-- Error --}}
                @error('name')
                <p class="text-danger small">{{ $message }}</p>
                @enderror

            </div>
            <div class="mb-3">
                <label for="email" class="form-label fw-bold">E-mail Address</label>
                <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}">
                {{-- Error --}}
                @error('email')
                <p class="text-danger small">{{ $message }}</p>
                @enderror

            </div>

            <div class="mb-3">
                <label for="new_password" class="form-label fw-bold">New Password</label>
                <input type="password" name="new_password" id="new_password" class="form-control" placeholder="Add new password">
                @error('new_password')
                    <p class="text-danger small">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-3">
                <label for="new_password_confirmation" class="form-label fw-bold">Confirm New Password</label>
                <input type="password" name="new_password_confirmation" id="new_password_confirmation"  class="form-control">

                {{-- Error --}}
                @if ($errors->has('new_password'))
                @endif
            </div>
            
            <div class="mb-3">
            <label for="introduction" class="form-label fw-bold">Introduction</label>
                <textarea type="introduction" name="introduction" rows="5" class="form-control"> {{ old('introduction', $user->introduction) }}</textarea>
                {{-- Error --}}
                @error('introduction')
                <p class="text-danger small">{{ $message }}</p>
                @enderror

            </div>
            <button type="submit" class="btn btn-warning px-5">Save</button>
        </form>
        </div>
    </div>
@endsection