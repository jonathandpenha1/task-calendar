@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="mb-3">
        <button class="btn btn-secondary" onclick="window.location.href='/'">
            <i class="bi bi-arrow-left"></i>
        </button>
    </div>

    <div class="mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Create Category</li>
            </ol>
        </nav>
    </div>


    <!-- Create Category Form Section -->
    <div class="row justify-content-center mb-4">
        <div class="col-lg-4 col-md-6 col-sm-8">
            <!-- Create Category Card -->
            <div class="card shadow-sm p-3">
                <h4 class="text-center mb-3">Create Category</h4>
                <form action="{{ route('categories.store') }}" method="POST">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="name" class="form-label">Category Name</label>
                        <input type="text" class="form-control form-control-sm w-100" id="name" name="name" required placeholder="Enter category name">
                    </div>
                    <div class="form-group mb-3">
                        <label for="color" class="form-label">Category Color</label>
                        <input type="color" class="form-control form-control-sm w-100 form-control-lg" id="color" name="color" value="#000000" required title="Choose a category color">
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-sm shadow-sm">Create Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Categories List Section -->
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10 col-sm-12">
            <!-- Categories List Card -->
            <div class="card shadow-sm p-3">
                <h4 class="text-center mb-3">Categories</h4>
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
                    @foreach ($categories as $category)
                        <div class="col">
                            <div class="card shadow-sm" style="background-color: {{ $category->color }}; color: white; border-radius: 8px; transition: transform 0.3s;">
                                <div class="card-body text-center">
                                    <h5 class="card-title fw-bold mb-2">{{ $category->name }}</h5>
                                    <form action="{{ route('categories.destroy', $category->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm mt-2 shadow-sm">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
