@extends('admin.layouts.app')

@section('title', 'Products')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Products</h2>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-secondary">Back to Dashboard</a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.products.index') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by name...">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-select">
                        <option value="">All categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->categoryId }}" {{ (string)request('category_id') === (string)$cat->categoryId ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All statuses</option>
                        @php($statuses = ['available' => 'Available', 'sold' => 'Sold'])
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Seller</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                        <tr>
                            <td>{{ $product->productId }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @php($thumb = optional($product->images->first())->image_path)
                                    <img src="{{ $thumb ? asset('storage/' . $thumb) : asset('images/default-product.png') }}" alt="thumb" class="rounded me-2" width="40" height="40" style="object-fit:cover;">
                                    <div>
                                        <div class="fw-semibold">{{ $product->name }}</div>
                                        <small class="text-muted">ID: {{ $product->productId }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $product->user?->name ?? '—' }}</div>
                                @if($product->user)
                                    <small class="text-muted">ID: {{ $product->user->userId }}</small>
                                @endif
                            </td>
                            <td>{{ $product->category?->name ?? '—' }}</td>
                            <td>₹{{ number_format((float)$product->price, 2) }}</td>
                            <td>
                                @php($status = strtolower((string)$product->status))
                                <span class="badge {{ $status === 'sold' ? 'bg-danger' : 'bg-success' }}">{{ ucfirst($status ?: 'unknown') }}</span>
                            </td>
                            <td>{{ $product->created_at?->format('M d, Y') }}</td>
                            <td class="text-end">
                                <a href="{{ route('products.show', $product->productId) }}" class="btn btn-sm btn-outline-primary">View</a>
                                <form action="{{ route('admin.products.destroy', $product->productId) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger ms-1">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No products found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if(method_exists($products, 'links'))
        <div class="card-footer d-flex justify-content-between align-items-center">
            <div>
                <small>Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }} products</small>
            </div>
            <div>
                {{ $products->withQueryString()->links() }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
