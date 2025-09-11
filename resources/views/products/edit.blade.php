@extends('layouts.app')

@section('title', 'Edit Product - ReWear')

@section('content')
<div class="edit-product-page">
    <div class="form-container">
        <div class="form-header">
            <h1>Edit your listing</h1>
            <p>Update your product information</p>
        </div>
        
        <form method="POST" action="{{ route('products.update', $product->productId) }}" enctype="multipart/form-data" class="product-form">
            @csrf
            @method('PUT')
            
            <div class="form-section">
                <h3>Basic Information</h3>
                
                <div class="form-group">
                    <label for="name">Product Title <span class="required">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <p class="form-tip">Include brand, model, and key features</p>
                </div>
                
                <div class="form-group">
                    <label for="categoryId">Category <span class="required">*</span></label>
                    <select id="categoryId" name="categoryId" class="form-control @error('categoryId') is-invalid @enderror" required>
                        <option value="">Select Category</option>
                        @foreach($categoryGroups as $mainCategory)
                            @if($mainCategory->children && $mainCategory->children->count() > 0)
                                <optgroup label="{{ $mainCategory->name }}">
                                    <!-- Allow selecting the main category itself -->
                                    <option value="{{ $mainCategory->categoryId }}" {{ (string)old('categoryId', $product->categoryId) === (string)$mainCategory->categoryId ? 'selected' : '' }}>
                                        {{ $mainCategory->name }}
                                    </option>
                                    @foreach($mainCategory->children as $subCategory)
                                        <option value="{{ $subCategory->categoryId }}" {{ (string)old('categoryId', $product->categoryId) === (string)$subCategory->categoryId ? 'selected' : '' }}>
                                            {{ $subCategory->name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @else
                                <!-- If no children, show the main category as a direct option -->
                                <option value="{{ $mainCategory->categoryId }}" {{ (string)old('categoryId', $product->categoryId) === (string)$mainCategory->categoryId ? 'selected' : '' }}>
                                    {{ $mainCategory->name }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                    @error('categoryId')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-row">
                    <div class="form-group col-6">
                        <label for="price">Price <span class="required">*</span></label>
                        <div class="price-input">
                            <span class="currency">₹</span>
                            <input type="number" id="price" name="price" value="{{ old('price', $product->price) }}" class="form-control @error('price') is-invalid @enderror" required min="0" step="0.01">
                        </div>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group col-6">
                        <label for="condition">Condition <span class="required">*</span></label>
                        <select id="condition" name="condition" class="form-control @error('condition') is-invalid @enderror" required>
                            <option value="">Select Condition</option>
                            <option value="new" {{ old('condition', $product->condition) == 'new' ? 'selected' : '' }}>New</option>
                            <option value="like_new" {{ old('condition', $product->condition) == 'like_new' ? 'selected' : '' }}>Like New</option>
                            <option value="good" {{ old('condition', $product->condition) == 'good' ? 'selected' : '' }}>Good</option>
                            <option value="fair" {{ old('condition', $product->condition) == 'fair' ? 'selected' : '' }}>Fair</option>
                            <option value="poor" {{ old('condition', $product->condition) == 'poor' ? 'selected' : '' }}>Poor</option>
                        </select>
                        @error('condition')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description">Description <span class="required">*</span></label>
                    <textarea id="description" name="description" rows="6" class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $product->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <p class="form-tip">Include details about features, condition, and reason for selling</p>
                </div>
                
                <div class="form-group">
                    <label for="status">Status <span class="required">*</span></label>
                    <select id="status" name="status" class="form-control @error('status') is-invalid @enderror" required>
                        <option value="available" {{ old('status', $product->status) == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="sold" {{ old('status', $product->status) == 'sold' ? 'selected' : '' }}>Sold</option>
                        <option value="unavailable" {{ old('status', $product->status) == 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="form-section">
                <h3>Photos</h3>
                <p class="section-description">Add up to 5 photos. Clear photos from multiple angles help buyers see your item better.</p>
                
                @if($product->images->count() > 0)
                <div class="current-images">
                    <h4>Current Images</h4>
                    <div class="images-grid">
                        @foreach($product->images as $image)
                            <div class="image-item">
                                <img src="{{ asset('storage/' . $image->image_path) }}" alt="Product image">
                                <div class="image-actions">
                                    <label class="delete-checkbox">
                                        <input type="checkbox" name="remove_images[]" value="{{ $image->id }}">
                                        <span class="checkmark"></span>
                                        <span>Delete</span>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
                
                <div class="upload-container">
                    <div class="upload-area" id="upload-area">
                        <input type="file" id="images" name="images[]" accept="image/*" multiple class="file-input @error('images') is-invalid @enderror @error('images.*') is-invalid @enderror">
                        <div class="upload-message">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Drag and drop photos or <span>Browse</span></p>
                            <small>(Max 5 photos, up to 5MB each)</small>
                        </div>
                    </div>
                    
                    <div class="preview-container" id="preview-container"></div>
                    
                    @error('images')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    
                    @error('images.*')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="submit-btn">Update Product</button>
                <a href="{{ route('products.show', $product->productId) }}" class="cancel-btn">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Upload preview functionality
    const fileInput = document.getElementById('images');
    const previewContainer = document.getElementById('preview-container');
    const uploadArea = document.getElementById('upload-area');
    
    // Handle file selection
    fileInput.addEventListener('change', function() {
        previewContainer.innerHTML = '';
        
        if (this.files) {
            [...this.files].forEach(file => {
                if (!file.type.match('image.*')) return;
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.createElement('div');
                    preview.className = 'preview-item';
                    
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = 'Preview';
                    
                    const removeBtn = document.createElement('button');
                    removeBtn.className = 'remove-btn';
                    removeBtn.innerHTML = '<i class="fas fa-times"></i>';
                    removeBtn.onclick = function() {
                        preview.remove();
                    };
                    
                    preview.appendChild(img);
                    preview.appendChild(removeBtn);
                    previewContainer.appendChild(preview);
                };
                
                reader.readAsDataURL(file);
            });
        }
    });
    
    // Drag and drop functionality
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, preventDefaults, false);
    });
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    ['dragenter', 'dragover'].forEach(eventName => {
        uploadArea.addEventListener(eventName, highlight, false);
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, unhighlight, false);
    });
    
    function highlight() {
        uploadArea.classList.add('highlight');
    }
    
    function unhighlight() {
        uploadArea.classList.remove('highlight');
    }
    
    uploadArea.addEventListener('drop', handleDrop, false);
    
    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        fileInput.files = files;
        
        // Trigger change event
        const event = new Event('change', { bubbles: true });
        fileInput.dispatchEvent(event);
    }
</script>
@endsection