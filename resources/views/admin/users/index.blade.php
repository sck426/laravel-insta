@extends('layouts.app')

@section('title', 'Admin: Users')

@section('content')
    <table class="table table-hover align-middle bg-white borde text-secondary">
        <thead class="table-success text-secondary">
            <th></th>
            <th>NAME</th>
            <th>EMAIL</th>
            <th>CREATED AT</th>
            <th>STATUS</th>
            <th></th>
        </thead>
        <tbody>
            @foreach ($all_users as $user)
            <tr>
                <td>
                    @if ($user->avatar)
                    <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="rounded-circle mx-auto d-block avatar-md">
                    @else
                    <i class="fas fa-circle-user d-block mx-auto text-center icon-md"></i>
                    @endif
                </td>
                <td>
                    <a href="{{ route('profile.show',$user->id) }}" class="text-decoration-none text-dark fw-bold">{{ $user->name }}</a>
                </td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->created_at }}</td>
                <td>
                    {{-- if the user is DOFT DELETED --}}
                    @if ($user->trashed())
                    <i class="far fa-circle text-secondary"></i> &nbsp; Inactive
                    @else
                    <i class="fas fa-circle text-success"></i> &nbsp; Active
                    @endif
                </td>
                <td>
                    @if (Auth::user()->id !==$user->id)
                     <div class="dropdown">
                        <button class="btn btn-sm" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis"></i>
                        </button>

                        <div class="dropdown-menu">
                           @if ($user->trashed())
                             <button class="dropdown-item text-success" data-bs-toggle="modal"data-bs-target="#activate-user-{{ $user->id }}">
                             <i class="fas fa-user-check"></i> Activate {{ $user->name }}
                             </button>
                           @else
                             <button class="dropdown-item text-danger" data-bs-toggle="modal"data-bs-target="#deactivate-user-{{ $user->id }}">
                             <i class="fas fa-user-slash"></i> Deactivate {{ $user->name }}
                             </button>
                           @endif
                      </div>
                     </div>
                     {{-- include modal here --}}
                     @include('admin.users.modal.status')
                     @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $all_users->links() }}
@endsection