@extends('layouts.app')

@section('title', 'Edit Category')

@section('content')
    <div class="admin-container">
        <h1>Edit Category</h1>
        
        <div class="admin-nav">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <a href="{{ route('admin.users.index') }}">Users</a>
            <a href="{{ route('admin.products.index') }}">Products</a>
            <a href="{{ route('admin.orders.index') }}">Orders</a>
            <a href="{{ route('admin.categories.index') }}">Categories</a>
            <a href="{{ route('admin.reports.index') }}">Reports</a>
        </div>
        
        <div class="form-container">
            <form method="POST" action="{{ route('admin.categories.update', $category->categoryId) }}">
                @csrf
                @method('PUT')
                
                <div class="form-group">
                    <label for="name">Category Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}" required>
                    @error('name')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-buttons">
                    <button type="submit" class="btn-primary">Update Category</button>
                    <a href="{{ route('admin.categories.index') }}" class="btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection