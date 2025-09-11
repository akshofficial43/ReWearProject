@extends('layouts.app')

@section('title', 'Manage Categories')

@section('content')
    <div class="admin-container">
        <h1>Manage Categories</h1>
        
        <div class="admin-nav">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <a href="{{ route('admin.users.index') }}">Users</a>
            <a href="{{ route('admin.products.index') }}">Products</a>
            <a href="{{ route('admin.orders.index') }}">Orders</a>
            <a href="{{ route('admin.categories.index') }}" class="active">Categories</a>
            <a href="{{ route('admin.reports.index') }}">Reports</a>
        </div>
        
        <div class="admin-content">
            <div class="admin-actions">
                <a href="{{ route('admin.categories.create') }}" class="btn-primary">Add New Category</a>
            </div>
            
            <div class="admin-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Products Count</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $category)
                            <tr>
                                <td>{{ $category->categoryId }}</td>
                                <td>{{ $category->name }}</td>
                                <td>{{ $category->products_count }}</td>
                                <td>
                                    <a href="{{ route('admin.categories.edit', $category->categoryId) }}" class="btn-small">Edit</a>
                                    <form action="{{ route('admin.categories.delete', $category->categoryId) }}" method="POST" class="inline-form" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-small danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="pagination">
                {{ $categories->links() }}
            </div>
        </div>
    </div>
@endsection