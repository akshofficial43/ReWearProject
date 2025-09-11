@extends('admin.layouts.app')

@section('title', 'Official Products')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Official Products</h1>
    <a href="{{ route('admin.official-products.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Add Official Product
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:72px">Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th style="width:120px">Price</th>
                        <th style="width:120px">Status</th>
                        <th style="width:160px">Created</th>
                        <th style="width:100px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-thumbnail" style="width:64px;height:64px;object-fit:cover;">
                                @else
                                    <img src="https://via.placeholder.com/64?text=IMG" alt="No image" class="img-thumbnail" style="width:64px;height:64px;object-fit:cover;">
                                @endif
                            </td>
                            <td class="fw-semibold">{{ $product->name }}</td>
                            <td>{{ optional($product->category)->name ?? '-' }}</td>
                            <td>₹{{ number_format($product->price, 2) }}</td>
                            <td>
                                @php($status = strtolower((string)$product->status))
                                <span class="badge {{ $status === 'sold' ? 'bg-danger' : 'bg-success' }}">
                                    {{ ucfirst($status ?: 'unknown') }}
                                </span>
                            </td>
                            <td>{{ $product->created_at?->format('Y-m-d H:i') }}</td>
                            <td class="d-flex gap-2">
                                <a href="{{ route('admin.official-products.edit', $product->productId) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.official-products.destroy', $product->productId) }}" method="POST" onsubmit="return confirm('Delete this official product?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No official products found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        {{ $products->links() }}
    </div>
  </div>
@endsection
