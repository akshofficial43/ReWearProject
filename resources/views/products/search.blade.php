@extends('layouts.app')

@section('title', 'Search Results - ReWear')

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
                    if ($activeCategoryId ?? false) {
                        try {
                            $crumbCategory = \App\Models\Category::find($activeCategoryId);
                        } catch (\Throwable $e) {
                            $crumbCategory = null;
                        }
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
                <span>Search Results</span>
                @if(request('q'))
                    <i class="fas fa-chevron-right"></i>
                    <span class="search-term">"{{ request('q') }}"</span>
                @endif
            </div>
        </div>
    </div>
    
    <div class="amazon-content-wrapper">
        <!-- Left Sidebar Filters -->
        <div class="amazon-sidebar" id="mobile-filters">
            <div class="mobile-filter-header">
                <h3>Filters</h3>
                <button type="button" class="close-filters-btn" onclick="toggleMobileFilters()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="filter-section">
                <h3 class="filter-title">Department</h3>
                <div class="filter-options">
                    @foreach($categories->take(8) as $category)
                        <label class="filter-option">
                            <input type="checkbox" 
                                   name="categories[]" 
                                   value="{{ $category->categoryId }}"
                                   {{ (request()->has('categories') && in_array($category->categoryId, (array)request('categories'))) || request('category') == $category->categoryId ? 'checked' : '' }}
                                   onchange="applyFilter()">
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
                        <input type="number" 
                               name="min_price" 
                               placeholder="₹ Min" 
                               value="{{ request('min_price') }}"
                               class="price-input"
                               onchange="applyFilter()">
                        <span class="price-separator">to</span>
                        <input type="number" 
                               name="max_price" 
                               placeholder="₹ Max" 
                               value="{{ request('max_price') }}"
                               class="price-input"
                               onchange="applyFilter()">
                    </div>
                    <button type="button" class="price-go-btn" onclick="applyFilter()">Go</button>
                </div>
            </div>

            <div class="filter-section">
                <h3 class="filter-title">Condition</h3>
                <div class="filter-options">
                    @foreach(['new' => 'New', 'like_new' => 'Like New', 'good' => 'Good', 'fair' => 'Fair', 'poor' => 'Poor'] as $value => $label)
                        <label class="filter-option">
                            <input type="checkbox" 
                                   name="condition[]" 
                                   value="{{ $value }}"
                                   {{ in_array($value, (array)request('condition')) ? 'checked' : '' }}
                                   onchange="applyFilter()">
                            <span class="checkmark"></span>
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="filter-section">
                <h3 class="filter-title">Location</h3>
                <div class="location-filter">
                    <input type="text" 
                           name="location" 
                           placeholder="Enter location" 
                           value="{{ request('location') }}"
                           class="location-input"
                           onchange="applyFilter()">
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="amazon-main-content">
            <!-- Mobile Filter Button -->
            <div class="mobile-filter-toggle">
                <button type="button" class="filter-toggle-btn" onclick="toggleMobileFilters()">
                    <i class="fas fa-filter"></i>
                    Filters
                    <span class="filter-count" id="filter-count" style="display: none;"></span>
                </button>
            </div>

            <!-- Results Header -->
            <div class="results-header">
                <div class="results-info">
                    <h1 class="results-title">
                        @if($searchStats['search_term'])
                            Results for "{{ $searchStats['search_term'] }}"
                        @else
                            All Results
                        @endif
                    </h1>
                    <span class="results-count">
                        {{ number_format($searchStats['total_results']) }} results
                    </span>
                </div>
                <div class="sort-section">
                    <span class="sort-label">Sort by:</span>
                    <select name="sort" class="sort-dropdown" onchange="applyFilter()">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest Arrivals</option>
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Alphabetical: A-Z</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                    </select>
                </div>
            </div>

            <!-- Applied Filters -->
            @if(!empty($searchStats['filters_applied']))
                <div class="applied-filters-bar">
                    @foreach($searchStats['filters_applied'] as $filter)
                        <span class="applied-filter">
                            {{ $filter }}
                            <button type="button" class="remove-filter" onclick="removeFilter('{{ $filter }}')">&times;</button>
                        </span>
                    @endforeach
                    <a href="{{ route('products.search') }}" class="clear-all-filters">Clear all filters</a>
                </div>
            @endif
            
            <!-- Product Grid -->
            <div class="amazon-product-grid">
                @forelse($products as $product)
                    <div class="amazon-product-card">
                        <a href="{{ route('products.show', $product->productId) }}" class="product-card-link">
                            <div class="product-image-container">
                                @if($product->images->count() > 0)
                                    <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" 
                                         alt="{{ $product->name }}" 
                                         class="product-image">
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
                                        {{ $product->location }}
                                    </div>
                                    <div class="product-seller">
                                        by {{ $product->user->name }}
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="no-results-amazon">
                        <div class="no-results-content">
                            <div class="no-results-icon">
                                <i class="fas fa-search"></i>
                            </div>
                            <h2>No results for "{{ request('q') ?: 'your search' }}"</h2>
                            <div class="suggestions">
                                <p>Try checking your spelling or use more general terms</p>
                                <div class="suggested-actions">
                                    <a href="{{ route('products.index') }}" class="amazon-btn primary">Browse All Products</a>
                                    <button type="button" class="amazon-btn secondary" onclick="showPopularSearches()">
                                        Popular Searches
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
            
            <!-- Pagination -->
            @if($products->hasPages())
                <div class="amazon-pagination">
                    <div class="pagination-wrapper">
                        {{ $products->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Hidden form for filter changes -->
<form id="filter-form" action="{{ route('products.search') }}" method="GET" style="display: none;">
    <input type="hidden" name="q" value="{{ request('q') }}">
    <input type="hidden" name="category" value="{{ request('category') }}">
    <input type="hidden" name="min_price" value="{{ request('min_price') }}">
    <input type="hidden" name="max_price" value="{{ request('max_price') }}">
    <input type="hidden" name="location" value="{{ request('location') }}">
    <input type="hidden" name="sort" value="{{ request('sort') }}">
    @foreach((array)request('categories') as $cat)
        <input type="hidden" name="categories[]" value="{{ $cat }}">
    @endforeach
    @foreach((array)request('condition') as $condition)
        <input type="hidden" name="condition[]" value="{{ $condition }}">
    @endforeach
</form>

<!-- Popular Searches Modal -->
<div id="popular-searches-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Popular Searches</h3>
            <span class="close" onclick="closePopularSearches()">&times;</span>
        </div>
        <div class="modal-body">
            <div class="popular-categories">
                <h4>Popular Categories</h4>
                <div id="popular-categories-list"></div>
            </div>
            <div class="popular-locations">
                <h4>Popular Locations</h4>
                <div id="popular-locations-list"></div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('styles')
<style>
/* Amazon-style Search Page */
.amazon-search-page {
    background-color: #ffffff;
    min-height: 100vh;
}

/* Top Search Section */
.top-search-section {
    background: #232f3e;
    padding: 12px 0;
    border-bottom: 1px solid #ddd;
}

.search-bar-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.amazon-search-form {
    max-width: 800px;
    margin: 0 auto;
}

.search-input-wrapper {
    display: flex;
    background: white;
    border-radius: 4px;
    overflow: hidden;
    box-shadow: 0 2px 5px rgba(15,17,17,.15);
}

.category-dropdown {
    background: #f3f3f3;
    border: none;
    padding: 10px 8px;
    font-size: 12px;
    color: #0f1111;
    border-right: 1px solid #cdcdcd;
    min-width: 80px;
    max-width: 120px;
    cursor: pointer;
}

.category-dropdown:focus {
    outline: none;
    background: #e6e6e6;
}

.search-field {
    flex: 1;
    position: relative;
}

.main-search-input {
    width: 100%;
    padding: 10px 12px;
    border: none;
    font-size: 16px;
    outline: none;
}

.search-submit-btn {
    background: #febd69;
    border: none;
    padding: 0 20px;
    cursor: pointer;
    transition: background-color 0.2s;
}

.search-submit-btn:hover {
    background: #f3a847;
}

.search-submit-btn i {
    font-size: 16px;
    color: #0f1111;
}

/* Amazon Suggestions */
.amazon-suggestions {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ddd;
    border-top: none;
    box-shadow: 0 2px 5px rgba(15,17,17,.15);
    z-index: 1000;
    display: none;
}

.suggestion-group {
    padding: 8px 0;
    border-bottom: 1px solid #e7e7e7;
}

.suggestion-group:last-child {
    border-bottom: none;
}

.suggestion-group h5 {
    padding: 4px 12px;
    margin: 0;
    font-size: 11px;
    color: #565959;
    font-weight: bold;
    text-transform: uppercase;
}

.suggestion-item {
    padding: 6px 12px;
    cursor: pointer;
    font-size: 14px;
    color: #0f1111;
}

.suggestion-item:hover {
    background: #f0f2f2;
}

/* Breadcrumb */
.amazon-breadcrumb {
    background: #f3f3f3;
    border-bottom: 1px solid #ddd;
    padding: 8px 0;
}

.breadcrumb-wrapper {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
    font-size: 12px;
    color: #565959;
}

.breadcrumb-wrapper a {
    color: #007185;
    text-decoration: none;
}

.breadcrumb-wrapper a:hover {
    color: #c7511f;
    text-decoration: underline;
}

.separator {
    margin: 0 6px;
    color: #565959;
}

.search-term {
    font-weight: bold;
    color: #0f1111;
}

/* Content Wrapper */
.amazon-content-wrapper {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    display: grid;
    grid-template-columns: 240px 1fr;
    gap: 20px;
}

/* Sidebar */
.amazon-sidebar {
    background: white;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 0;
    height: fit-content;
}

.mobile-filter-header {
    display: none;
    padding: 16px;
    border-bottom: 1px solid #e7e7e7;
    background: #f8f9fa;
    border-radius: 8px 8px 0 0;
    align-items: center;
    justify-content: space-between;
}

.mobile-filter-header h3 {
    margin: 0;
    font-size: 18px;
    color: #0f1111;
}

.close-filters-btn {
    background: none;
    border: none;
    font-size: 20px;
    color: #565959;
    cursor: pointer;
    padding: 4px;
}

.mobile-filter-toggle {
    display: none;
    margin-bottom: 16px;
}

.filter-toggle-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #007185;
    color: white;
    border: none;
    padding: 12px 16px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: bold;
    cursor: pointer;
    width: 100%;
    justify-content: center;
    position: relative;
}

.filter-toggle-btn:hover {
    background: #005a6b;
}

.filter-count {
    background: #ff9900;
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    position: absolute;
    top: -2px;
    right: 8px;
}

.filter-section {
    border-bottom: 1px solid #e7e7e7;
    padding: 16px;
}

.filter-section:last-child {
    border-bottom: none;
}

.filter-title {
    font-size: 16px;
    font-weight: bold;
    color: #0f1111;
    margin: 0 0 12px 0;
}

.filter-options {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.filter-option {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    color: #0f1111;
    cursor: pointer;
    position: relative;
}

.filter-option input[type="checkbox"] {
    width: 16px;
    height: 16px;
    margin: 0;
    cursor: pointer;
}

.filter-option:hover {
    color: #007185;
}

/* Price Filter */
.price-filter {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.price-inputs {
    display: flex;
    align-items: center;
    gap: 8px;
    width: 100%;
}

.price-input {
    flex: 1;
    padding: 8px 10px;
    border: 1px solid #888c8c;
    border-radius: 4px;
    font-size: 14px;
    min-width: 0;
    text-align: center;
}

.price-input:focus {
    outline: none;
    border-color: #007185;
    box-shadow: 0 0 3px rgba(0, 113, 133, 0.3);
}

.price-separator {
    font-size: 12px;
    color: #565959;
    font-weight: bold;
    white-space: nowrap;
    padding: 0 4px;
}

.price-go-btn {
    background: #007185;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    font-size: 12px;
    cursor: pointer;
    font-weight: bold;
    transition: background-color 0.2s;
    white-space: nowrap;
}

.price-go-btn:hover {
    background: #005a6b;
}

.location-input {
    width: 100%;
    padding: 8px;
    border: 1px solid #888c8c;
    border-radius: 4px;
    font-size: 14px;
}

/* Main Content */
.amazon-main-content {
    background: white;
}

.results-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 0;
    border-bottom: 1px solid #e7e7e7;
    margin-bottom: 16px;
}

.results-title {
    font-size: 16px;
    font-weight: normal;
    color: #0f1111;
    margin: 0 0 4px 0;
}

.results-count {
    font-size: 14px;
    color: #565959;
}

.sort-section {
    display: flex;
    align-items: center;
    gap: 8px;
}

.sort-label {
    font-size: 14px;
    color: #0f1111;
}

.sort-dropdown {
    padding: 6px 8px;
    border: 1px solid #888c8c;
    border-radius: 4px;
    font-size: 14px;
    background: white;
    cursor: pointer;
}

/* Applied Filters Bar */
.applied-filters-bar {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 16px;
    flex-wrap: wrap;
}

.applied-filter {
    background: #e7f3f3;
    border: 1px solid #007185;
    border-radius: 4px;
    padding: 4px 8px;
    font-size: 12px;
    color: #0f1111;
    display: flex;
    align-items: center;
    gap: 4px;
}

.remove-filter {
    background: none;
    border: none;
    color: #007185;
    cursor: pointer;
    font-size: 14px;
    padding: 0;
    margin-left: 4px;
}

.clear-all-filters {
    color: #007185;
    text-decoration: none;
    font-size: 12px;
}

.clear-all-filters:hover {
    text-decoration: underline;
}

/* Product Grid */
.amazon-product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 16px;
}

.amazon-product-card {
    border: 1px solid #e7e7e7;
    border-radius: 12px;
    overflow: hidden;
    background: white;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.amazon-product-card:hover {
    box-shadow: 0 8px 16px rgba(15,17,17,.15);
    transform: translateY(-2px);
}

.product-card-link {
    text-decoration: none;
    color: inherit;
    display: block;
}

.product-image-container {
    position: relative;
    height: 220px;
    overflow: hidden;
    background: #f8f8f8;
    display: flex;
    align-items: center;
    justify-content: center;
}

.product-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.amazon-product-card:hover .product-image {
    transform: scale(1.08);
}

.no-image-placeholder {
    font-size: 48px;
    color: #ccc;
}

.product-condition-badge {
    position: absolute;
    top: 12px;
    right: 12px;
    background: linear-gradient(135deg, #007185, #005a6b);
    color: white;
    padding: 6px 10px;
    border-radius: 20px;
    font-size: 10px;
    text-transform: uppercase;
    font-weight: bold;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.product-details {
    padding: 16px;
}

.product-name {
    font-size: 15px;
    line-height: 1.4;
    color: #007185;
    margin: 0 0 10px 0;
    font-weight: 500;
    min-height: 42px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.product-name:hover {
    color: #c7511f;
}

.product-rating {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 10px;
}

.stars {
    display: flex;
    gap: 2px;
}

.stars i {
    font-size: 14px;
    color: #ddd;
}

.stars i.filled {
    color: #ffa41c;
}

.rating-count {
    font-size: 12px;
    color: #007185;
    font-weight: 500;
}

.product-price-section {
    display: flex;
    align-items: baseline;
    margin-bottom: 8px;
    gap: 3px;
}

.currency {
    font-size: 16px;
    color: #0f1111;
    font-weight: bold;
}

.price {
    font-size: 20px;
    font-weight: bold;
    color: #0f1111;
}

.product-meta {
    border-top: 1px solid #e7e7e7;
    padding-top: 10px;
    margin-top: 10px;
}

.product-location,
.product-seller {
    font-size: 12px;
    color: #565959;
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.product-location i,
.product-seller i {
    width: 12px;
    text-align: center;
}

/* No Results */
.no-results-amazon {
    grid-column: 1 / -1;
    text-align: center;
    padding: 40px 20px;
}

.no-results-content {
    max-width: 400px;
    margin: 0 auto;
}

.no-results-icon {
    font-size: 64px;
    color: #ddd;
    margin-bottom: 20px;
}

.no-results-amazon h2 {
    font-size: 24px;
    color: #0f1111;
    margin-bottom: 12px;
}

.suggestions p {
    color: #565959;
    margin-bottom: 20px;
}

.suggested-actions {
    display: flex;
    gap: 12px;
    justify-content: center;
}

.amazon-btn {
    padding: 8px 16px;
    border-radius: 4px;
    text-decoration: none;
    font-size: 14px;
    cursor: pointer;
    border: none;
    display: inline-block;
}

.amazon-btn.primary {
    background: #007185;
    color: white;
}

.amazon-btn.primary:hover {
    background: #005a6b;
}

.amazon-btn.secondary {
    background: #fff;
    color: #0f1111;
    border: 1px solid #d5d9d9;
}

.amazon-btn.secondary:hover {
    background: #f7f8f8;
}

/* Pagination */
.amazon-pagination {
    margin-top: 24px;
    padding-top: 16px;
    border-top: 1px solid #e7e7e7;
}

.pagination-wrapper {
    display: flex;
    justify-content: center;
}

/* Responsive Design */
@media (max-width: 768px) {
    .amazon-content-wrapper {
        grid-template-columns: 1fr;
        padding: 15px;
        gap: 0;
    }
    
    .amazon-sidebar {
        position: fixed;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100vh;
        z-index: 1000;
        transition: left 0.3s ease;
        overflow-y: auto;
        border-radius: 0;
        border: none;
    }
    
    .amazon-sidebar.show {
        left: 0;
    }
    
    .mobile-filter-header {
        display: flex;
    }
    
    .mobile-filter-toggle {
        display: block;
    }
    
    .results-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
        padding: 12px 0;
    }
    
    .sort-section {
        width: 100%;
        justify-content: space-between;
    }
    
    .sort-dropdown {
        flex: 1;
        max-width: 200px;
        margin-left: 8px;
    }
    
    .amazon-product-grid {
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 12px;
    }
    
    .amazon-product-card {
        border-radius: 8px;
    }
    
    .product-image-container {
        height: 160px;
    }
    
    .product-details {
        padding: 12px;
    }
    
    .product-name {
        font-size: 14px;
        min-height: 36px;
        -webkit-line-clamp: 2;
    }
    
    .product-price-section {
        margin-bottom: 6px;
    }
    
    .currency {
        font-size: 14px;
    }
    
    .price {
        font-size: 18px;
    }
    
    .product-condition-badge {
        top: 8px;
        right: 8px;
        padding: 4px 8px;
        font-size: 9px;
    }
    
    .breadcrumb-wrapper {
        padding: 0 15px;
    }
    
    .applied-filters-bar {
        flex-wrap: wrap;
        gap: 6px;
    }
    
    .applied-filter {
        font-size: 11px;
        padding: 3px 6px;
    }
}

@media (max-width: 480px) {
    .amazon-content-wrapper {
        padding: 10px;
    }
    
    .amazon-product-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }
    
    .product-image-container {
        height: 140px;
    }
    
    .product-details {
        padding: 10px;
    }
    
    .product-name {
        font-size: 13px;
        min-height: 32px;
    }
    
    .currency {
        font-size: 13px;
    }
    
    .price {
        font-size: 16px;
    }
    
    .product-rating {
        margin-bottom: 8px;
    }
    
    .stars i {
        font-size: 12px;
    }
    
    .rating-count {
        font-size: 11px;
    }
    
    .product-location,
    .product-seller {
        font-size: 11px;
    }
    
    .results-title {
        font-size: 15px;
    }
    
    .results-count {
        font-size: 13px;
    }
    
    .filter-toggle-btn {
        padding: 10px 14px;
        font-size: 13px;
    }
}

@media (max-width: 360px) {
    .amazon-product-grid {
        grid-template-columns: 1fr 1fr;
        gap: 8px;
    }
    
    .product-image-container {
        height: 120px;
    }
    
    .product-details {
        padding: 8px;
    }
    
    .product-name {
        font-size: 12px;
        min-height: 30px;
    }
    
    .currency {
        font-size: 12px;
    }
    
    .price {
        font-size: 15px;
    }
}
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('amazon-search-input');
    const suggestionsContainer = document.getElementById('amazon-suggestions');
    let searchTimeout;
    
    // Search suggestions
    if (searchInput && suggestionsContainer) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            if (query.length < 2) {
                suggestionsContainer.style.display = 'none';
                return;
            }
            
            searchTimeout = setTimeout(() => {
                fetch(`/api/search/suggestions?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        displaySuggestions(data);
                    })
                    .catch(error => {
                        console.error('Error fetching suggestions:', error);
                    });
            }, 300);
        });
        
        // Hide suggestions when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !suggestionsContainer.contains(e.target)) {
                suggestionsContainer.style.display = 'none';
            }
        });
    }
    
    function displaySuggestions(data) {
        let html = '';
        
        if (data.products && data.products.length > 0) {
            html += '<div class="suggestion-group">';
            html += '<h5>Products</h5>';
            data.products.slice(0, 5).forEach(product => {
                html += `<div class="suggestion-item" onclick="selectSuggestion('${escapeHtml(product)}')">${escapeHtml(product)}</div>`;
            });
            html += '</div>';
        }
        
        if (data.categories && data.categories.length > 0) {
            html += '<div class="suggestion-group">';
            html += '<h5>Categories</h5>';
            data.categories.slice(0, 3).forEach(category => {
                html += `<div class="suggestion-item" onclick="selectSuggestion('${escapeHtml(category)}')">${escapeHtml(category)}</div>`;
            });
            html += '</div>';
        }
        
        if (data.locations && data.locations.length > 0) {
            html += '<div class="suggestion-group">';
            html += '<h5>Locations</h5>';
            data.locations.slice(0, 3).forEach(location => {
                html += `<div class="suggestion-item" onclick="selectSuggestion('${escapeHtml(location)}')">${escapeHtml(location)}</div>`;
            });
            html += '</div>';
        }
        
        if (html) {
            suggestionsContainer.innerHTML = html;
            suggestionsContainer.style.display = 'block';
        } else {
            suggestionsContainer.style.display = 'none';
        }
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});

function selectSuggestion(suggestion) {
    const searchInput = document.getElementById('amazon-search-input');
    const suggestionsContainer = document.getElementById('amazon-suggestions');
    
    if (searchInput) {
        searchInput.value = suggestion;
    }
    if (suggestionsContainer) {
        suggestionsContainer.style.display = 'none';
    }
}

function applyFilter() {
    const form = document.getElementById('filter-form');
    if (!form) return;
    
    // Update form values from current filters
    const selectedCategories = Array.from(document.querySelectorAll('input[name="categories[]"]:checked')).map(i => i.value);
    const minPriceInput = document.querySelector('input[name="min_price"]');
    const maxPriceInput = document.querySelector('input[name="max_price"]');
    const locationInput = document.querySelector('input[name="location"]');
    const sortSelect = document.querySelector('select[name="sort"]');
    const conditionCheckboxes = document.querySelectorAll('input[name="condition[]"]:checked');
    
    // Clear existing hidden categories and rewrite
    form.querySelectorAll('input[name="categories[]"]').forEach(n => n.remove());
    selectedCategories.forEach(val => {
        const hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = 'categories[]';
        hidden.value = val;
        form.appendChild(hidden);
    });
    
    if (minPriceInput) {
        form.querySelector('input[name="min_price"]').value = minPriceInput.value;
    }
    
    if (maxPriceInput) {
        form.querySelector('input[name="max_price"]').value = maxPriceInput.value;
    }
    
    if (locationInput) {
        form.querySelector('input[name="location"]').value = locationInput.value;
    }
    
    if (sortSelect) {
        form.querySelector('input[name="sort"]').value = sortSelect.value;
    }
    
    // Clear existing condition inputs
    const existingConditions = form.querySelectorAll('input[name="condition[]"]');
    existingConditions.forEach(input => input.remove());
    
    // Add new condition inputs
    conditionCheckboxes.forEach(checkbox => {
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'condition[]';
        hiddenInput.value = checkbox.value;
        form.appendChild(hiddenInput);
    });
    
    // Submit form
    form.submit();
}

function showPopularSearches() {
    fetch('/api/search/popular')
        .then(response => response.json())
        .then(data => {
            let categoriesHtml = '';
            if (data.categories) {
                data.categories.forEach(category => {
                    categoriesHtml += `<span class="popular-item" onclick="searchPopular('${escapeHtml(category)}')">${escapeHtml(category)}</span>`;
                });
            }
            document.getElementById('popular-categories-list').innerHTML = categoriesHtml;
            
            let locationsHtml = '';
            if (data.locations) {
                data.locations.forEach(location => {
                    locationsHtml += `<span class="popular-item" onclick="searchPopular('${escapeHtml(location)}')">${escapeHtml(location)}</span>`;
                });
            }
            document.getElementById('popular-locations-list').innerHTML = locationsHtml;
            
            document.getElementById('popular-searches-modal').style.display = 'block';
        })
        .catch(error => {
            console.error('Error fetching popular searches:', error);
        });
}

function closePopularSearches() {
    const modal = document.getElementById('popular-searches-modal');
    if (modal) {
        modal.style.display = 'none';
    }
}

function searchPopular(term) {
    const searchInput = document.getElementById('amazon-search-input');
    if (searchInput) {
        searchInput.value = term;
    }
    closePopularSearches();
    
    // Update the form and submit
    const form = document.getElementById('filter-form');
    if (form) {
        form.querySelector('input[name="q"]').value = term;
        form.submit();
    }
}

function removeFilter(filterText) {
    // This would need more complex logic to determine which filter to remove
    // For now, just redirect to clear that specific filter
    console.log('Remove filter:', filterText);
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>
@endsection
