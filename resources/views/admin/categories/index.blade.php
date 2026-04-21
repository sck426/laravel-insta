@extends('layouts.app')

@section('title', 'Admin: Categories')

@section('content')
            <form action="{{ route('admin.categories.store') }}" method="post" class="row gx-2 mb-4">
                @csrf
                <div class="col-4">
                    <input type="text" name="name" class="form-control" placeholder="Add a category..." autofocus>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add
                    </button>
                </div>

                @error('name')
                    <p class="text-danger small">{{ $message }}</p>
                @enderror
            </form>

            <table class="table table-hover align-middle bg-white border text-center text-secondary">
                <thead class="table-warning text-secondary small text-uppercase">
                    <tr>
                        <th>#</th>
                        <th>NAME</th>
                        <th>COUNT</th>
                        <th>LAST UPDATED</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($all_categories as $category)
                    <tr>
                        <td>{{ $category->id }}</td>
                        <td class="text-dark">{{ $category->name }}</td>
                        <td>{{ $category->categoryPost->count() }}</td>
                        <td>{{ $category->updated_at }}</td>
                        <td>
                            {{-- Edit --}}
                            <button class="btn btn-outline-warning btn-sm me-2" data-bs-toggle="modal" data-bs-target="#edit-category-{{ $category->id }}" title="Edit">
                                <i class="fas fa-pen"></i>
                            </button>
                            {{-- Delete --}}
                            <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#delete-category-{{ $category->id }}" title="Delete">
                                <i class="fas fa-trash-can"></i>
                            </button>

                            @include('admin.categories.modal.action')
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="lead text-muted">No categories found.</td>
                    </tr>
                    @endforelse
                    
                    {{-- Uncategorized --}}
                    <tr>
                        <td></td>
                        <td class="text-muted">
                            Uncategorized
                            <p class="xsmall text-muted m-0" style="font-size: 0.7rem;">Hidden posts are not included.</p>
                        </td>
                        <td>{{ $uncategorized_count }}</td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection