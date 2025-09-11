@extends('layouts.app')

@section('title', 'All Products - ReWear')

@section('content')
<div class="amazon-search-page">
    <!-- Breadcrumb -->
    <div class="breadcrumb-container">
        <div class="container">
            <div class="breadcrumb">
                @php
                    $activeCategoryId = request('category');
                    if (!$activeCategoryId && request()->has('categories') && count((array)request('categories')) === 1) {
                        $activeCategoryId = (array)request('categories');
                        $activeCategoryId = $activeCategoryId[0] ?? null;
                    }
                    $crumbCategory = null;
                    if ($activeCategoryId && isset($categories)) {
                        $crumbCategory = $categories->firstWhere('categoryId', (int)$activeCategoryId);
                    }
                @endphp
                <a href="{{ route('home') }}">Home</a>
                @if($crumbCategory)
                    <i class="fas fa-chevron-right"></i>
                    <a href="{{ route('products.index', ['category' => $crumbCategory->categoryId]) }}">{{ $crumbCategory->name }}</a>
                @elseif(request()->has('categories') && count((array)request('categories')) > 1)
                    <i class="fas fa-chevron-right"></i>
                    <span>Multiple categories</span>
                @endif
                <i class="fas fa-chevron-right"></i>
                <span>All Products</span>
            </div>
        </div>
    </div>

    <div class="amazon-content-wrapper">
        <!-- Sidebar Filters -->
        <div class="amazon-sidebar" id="mobile-filters">
            <div class="mobile-filter-header">
                <h3>Filters</h3>
                <button type="button" class="close-filters-btn" onclick="toggleMobileFilters()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form action="{{ route('products.index') }}" method="GET" id="filters-form">
                <div class="filter-section">
                    <h3 class="filter-title">Department</h3>
                    <div class="filter-options">
                        @foreach(($categories ?? collect())->take(10) as $category)
                            <label class="filter-option">
                                <input type="checkbox" name="categories[]" value="{{ $category->categoryId }}" {{ request()->has('categories') && in_array($category->categoryId, (array)request('categories')) ? 'checked' : '' }} onchange="applyIndexFilter()">
                                <span class="checkmark"></span>
                                {{ $category->name }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="filter-section">
                    <h3 class="filter-title">Price</h3>
                    <div class="price-filter">
                        <div class="price-inputs">
                            <input type="number" name="price_min" placeholder="₹ Min" value="{{ request('price_min') }}" class="price-input" onchange="applyIndexFilter()">
                            <span class="price-separator">to</span>
                            <input type="number" name="price_max" placeholder="₹ Max" value="{{ request('price_max') }}" class="price-input" onchange="applyIndexFilter()">
                        </div>
                        <button type="button" class="price-go-btn" onclick="applyIndexFilter()">Go</button>
                    </div>
                </div>

                <div class="filter-section">
                    <h3 class="filter-title">Condition</h3>
                    <div class="filter-options">
                        @foreach(['new' => 'New', 'like_new' => 'Like New', 'good' => 'Good', 'fair' => 'Fair', 'poor' => 'Poor'] as $value => $label)
                            <label class="filter-option">
                                <input type="checkbox" name="condition[]" value="{{ $value }}" {{ in_array($value, (array)request('condition')) ? 'checked' : '' }} onchange="applyIndexFilter()">
                                <span class="checkmark"></span>
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="filter-section">
                    <h3 class="filter-title">Location</h3>
                    <div class="location-filter">
                        <input type="text" name="location" placeholder="Enter location" value="{{ request('location') }}" class="location-input" onchange="applyIndexFilter()">
                    </div>
                </div>

                <!-- Sort moved to header to match search layout -->
            </form>
        </div>

        <!-- Main Content -->
        <div class="amazon-main-content">
            <!-- Mobile Filter Toggle -->
            <div class="mobile-filter-toggle">
                <button type="button" class="filter-toggle-btn" onclick="toggleMobileFilters()">
                    <i class="fas fa-filter"></i>
                    Filters
                </button>
            </div>

            <div class="results-header">
                <div class="results-info">
                    <h1 class="results-title">All Products</h1>
                    <span class="results-count">{{ number_format($products->total()) }} results</span>
                </div>
                <div class="sort-section">
                    <span class="sort-label">Sort by:</span>
                    <select name="sort" class="sort-dropdown" form="filters-form" onchange="applyIndexFilter()">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest Arrivals</option>
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                    </select>
                </div>
            </div>

            <div class="amazon-product-grid">
                @forelse($products as $product)
                    <div class="amazon-product-card">
                        <a href="{{ route('products.show', $product->productId) }}" class="product-card-link">
                            <div class="product-image-container">
                                @if($product->images->count() > 0)
                                    <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" alt="{{ $product->name }}" class="product-image">
                                @else
                                    <div class="no-image-placeholder">
                                        <i class="fas fa-image"></i>
                                    </div>
                                @endif
                                <div class="product-condition-badge">{{ ucfirst(str_replace('_', ' ', $product->condition)) }}</div>
                            </div>
                            <div class="product-details">
                                <h3 class="product-name">{{ Str::limit($product->name, 60) }}</h3>
                                <div class="product-rating">
                                    <div class="stars">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= 4 ? 'filled' : '' }}"></i>
                                        @endfor
                                    </div>
                                    <span class="rating-count">({{ rand(10, 150) }})</span>
                                </div>
                                <div class="product-price-section">
                                    <span class="currency">₹</span>
                                    <span class="price">{{ number_format($product->price, 2) }}</span>
                                </div>
                                <div class="product-meta">
                                    <div class="product-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        {{ $product->location ?: 'India' }}
                                    </div>
                                    <div class="product-seller">by {{ $product->user->name }}</div>
                                </div>
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="no-results-amazon">
                        <div class="no-results-content">
                            <div class="no-results-icon"><i class="fas fa-search"></i></div>
                            <h2>No products found</h2>
                            <div class="suggestions">
                                <p>Try changing filters or browse everything again</p>
                                <a href="{{ route('products.index') }}" class="amazon-btn primary">Reset Filters</a>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>

            @if($products->hasPages())
                <div class="amazon-pagination">
                    <div class="pagination-wrapper">
                        {{ $products->appends(request()->query())->links('vendor.pagination.custom') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
/* Amazon-style Search Page (matched to search.blade.css) */
.amazon-search-page{background-color:#ffffff;min-height:100vh}
.amazon-breadcrumb{background:#f3f3f3;border-bottom:1px solid #ddd;padding:8px 0}
.breadcrumb-wrapper{max-width:1200px;margin:0 auto;padding:0 20px;font-size:12px;color:#565959}
.breadcrumb-wrapper a{color:#007185;text-decoration:none}
.breadcrumb-wrapper a:hover{color:#c7511f;text-decoration:underline}
.separator{margin:0 6px;color:#565959}
.amazon-content-wrapper{max-width:1200px;margin:0 auto;padding:20px;display:grid;grid-template-columns:240px 1fr;gap:20px}
.amazon-sidebar{background:#fff;border:1px solid #ddd;border-radius:8px;padding:0;height:fit-content}
.mobile-filter-header{display:none;padding:16px;border-bottom:1px solid #e7e7e7;background:#f8f9fa;border-radius:8px 8px 0 0;align-items:center;justify-content:space-between}
.mobile-filter-header h3{margin:0;font-size:18px;color:#0f1111}
.close-filters-btn{background:none;border:none;font-size:20px;color:#565959;cursor:pointer;padding:4px}
.mobile-filter-toggle{display:none;margin-bottom:16px}
.filter-toggle-btn{display:flex;align-items:center;gap:8px;background:#007185;color:#fff;border:none;padding:12px 16px;border-radius:6px;font-size:14px;font-weight:700;cursor:pointer;width:100%;justify-content:center;position:relative}
.filter-toggle-btn:hover{background:#005a6b}
.filter-section{border-bottom:1px solid #e7e7e7;padding:16px}
.filter-section:last-child{border-bottom:none}
.filter-title{font-size:16px;font-weight:700;color:#0f1111;margin:0 0 12px}
.filter-options{display:flex;flex-direction:column;gap:8px}
.filter-option{display:flex;align-items:center;gap:8px;font-size:14px;color:#0f1111;cursor:pointer;position:relative}
.filter-option input[type="checkbox"]{width:16px;height:16px;margin:0;cursor:pointer}
.filter-option:hover{color:#007185}
/* Price Filter */
.price-filter{display:flex;flex-direction:column;gap:12px}
.price-inputs{display:flex;align-items:center;gap:8px;width:100%}
.price-input{flex:1;padding:8px 10px;border:1px solid #888c8c;border-radius:4px;font-size:14px;min-width:0;text-align:center}
.price-input:focus{outline:none;border-color:#007185;box-shadow:0 0 3px rgba(0,113,133,.3)}
.price-separator{font-size:12px;color:#565959;font-weight:700;white-space:nowrap;padding:0 4px}
.price-go-btn{background:#007185;color:#fff;border:none;padding:8px 16px;border-radius:4px;font-size:12px;cursor:pointer;font-weight:700;transition:background-color .2s;white-space:nowrap}
.price-go-btn:hover{background:#005a6b}
.location-input{width:100%;padding:8px;border:1px solid #888c8c;border-radius:4px;font-size:14px}
/* Main Content */
.amazon-main-content{background:#fff}
.results-header{display:flex;justify-content:space-between;align-items:center;padding:16px 0;border-bottom:1px solid #e7e7e7;margin-bottom:16px}
.results-title{font-size:16px;font-weight:400;color:#0f1111;margin:0 0 4px}
.results-count{font-size:14px;color:#565959}
.sort-section{display:flex;align-items:center;gap:8px}
.sort-label{font-size:14px;color:#0f1111}
.sort-dropdown{padding:6px 8px;border:1px solid #888c8c;border-radius:4px;font-size:14px;background:#fff;cursor:pointer}
/* Product Grid */
.amazon-product-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px}
.amazon-product-card{border:1px solid #e7e7e7;border-radius:12px;overflow:hidden;background:#fff;transition:all .3s ease;box-shadow:0 2px 4px rgba(0,0,0,.1)}
.amazon-product-card:hover{box-shadow:0 8px 16px rgba(15,17,17,.15);transform:translateY(-2px)}
.product-card-link{text-decoration:none;color:inherit;display:block}
.product-image-container{position:relative;height:220px;overflow:hidden;background:#f8f8f8;display:flex;align-items:center;justify-content:center}
.product-image{width:100%;height:100%;object-fit:cover;transition:transform .3s ease}
.amazon-product-card:hover .product-image{transform:scale(1.08)}
.no-image-placeholder{font-size:48px;color:#ccc}
.product-condition-badge{position:absolute;top:12px;right:12px;background:linear-gradient(135deg,#007185,#005a6b);color:#fff;padding:6px 10px;border-radius:20px;font-size:10px;text-transform:uppercase;font-weight:700;box-shadow:0 2px 4px rgba(0,0,0,.2)}
.product-details{padding:16px}
.product-name{font-size:15px;line-height:1.4;color:#007185;margin:0 0 10px;font-weight:500;min-height:42px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.product-name:hover{color:#c7511f}
.product-rating{display:flex;align-items:center;gap:8px;margin-bottom:10px}
.stars{display:flex;gap:2px}
.stars i{font-size:14px;color:#ddd}
.stars i.filled{color:#ffa41c}
.rating-count{font-size:12px;color:#007185;font-weight:500}
.product-price-section{display:flex;align-items:baseline;margin-bottom:8px;gap:3px}
.currency{font-size:16px;color:#0f1111;font-weight:700}
.price{font-size:20px;font-weight:700;color:#0f1111}
.product-meta{border-top:1px solid #e7e7e7;padding-top:10px;margin-top:10px}
.product-location,.product-seller{font-size:12px;color:#565959;margin-bottom:4px;display:flex;align-items:center;gap:6px}
/* Empty state */
.no-results-amazon{grid-column:1/-1;text-align:center;padding:40px 20px}
.no-results-content{max-width:400px;margin:0 auto}
.no-results-icon{font-size:64px;color:#ddd;margin-bottom:20px}
.amazon-btn{padding:8px 16px;border-radius:4px;text-decoration:none;font-size:14px;cursor:pointer;border:none;display:inline-block}
.amazon-btn.primary{background:#007185;color:#fff}
.amazon-btn.primary:hover{background:#005a6b}
/* Pagination */
.amazon-pagination{margin-top:24px;padding-top:16px;border-top:1px solid #e7e7e7}
.pagination-wrapper{display:flex;justify-content:center}
/* Responsive */
@media(max-width:768px){
    .amazon-content-wrapper{grid-template-columns:1fr;padding:15px;gap:0}
    .amazon-sidebar{position:fixed;top:0;left:-100%;width:100%;height:100vh;z-index:1000;transition:left .3s ease;overflow-y:auto;border-radius:0;border:none}
    .amazon-sidebar.show{left:0}
    .mobile-filter-header{display:flex}
    .mobile-filter-toggle{display:block}
    .results-header{flex-direction:column;align-items:flex-start;gap:12px;padding:12px 0}
    .sort-section{width:100%;justify-content:space-between}
    .sort-dropdown{flex:1;max-width:200px;margin-left:8px}
    .amazon-product-grid{grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:12px}
    .amazon-product-card{border-radius:8px}
    .product-image-container{height:160px}
    .product-details{padding:12px}
    .product-name{font-size:14px;min-height:36px;-webkit-line-clamp:2}
    .currency{font-size:14px}
    .price{font-size:18px}
    .product-condition-badge{top:8px;right:8px;padding:4px 8px;font-size:9px}
}
@media(max-width:480px){
    .amazon-content-wrapper{padding:10px}
    .amazon-product-grid{grid-template-columns:repeat(2,1fr);gap:10px}
    .product-image-container{height:140px}
    .product-details{padding:10px}
    .product-name{font-size:13px;min-height:32px}
    .currency{font-size:13px}
    .price{font-size:16px}
}
</style>
@endsection

@section('scripts')
<script>
function toggleMobileFilters(){
    const sidebar = document.getElementById('mobile-filters');
    if(!sidebar) return; sidebar.classList.toggle('show');
}
function applyIndexFilter(){
    document.getElementById('filters-form').submit();
}
</script>
@endsection