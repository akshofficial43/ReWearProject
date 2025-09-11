@extends('layouts.app')

@section('title', 'Sell Item - ReWear')

@section('content')
<div class="edit-product-page">
    <div class="form-container">
        <div class="form-header">
            <h1>Post Your Ad</h1>
            <p>Sell your items quickly and easily</p>
        </div>
        
        <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" class="product-form">
            @csrf
            
            <div class="form-section">
                <h3>Basic Information</h3>
                
                <div class="form-group">
                    <label for="name">Product Title <span class="required">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <p class="form-tip">Include brand, model, and key features</p>
                </div>
                
                <div class="form-group">
                    <label for="categoryId">Category <span class="required">*</span></label>
                    <select id="categoryId" name="categoryId" class="form-control @error('categoryId') is-invalid @enderror" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->categoryId }}" {{ old('categoryId') == $category->categoryId ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
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
                            <input type="number" id="price" name="price" value="{{ old('price') }}" class="form-control @error('price') is-invalid @enderror" required min="0" step="0.01">
                        </div>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group col-6">
                        <label for="condition">Condition <span class="required">*</span></label>
                        <select id="condition" name="condition" class="form-control @error('condition') is-invalid @enderror" required>
                            <option value="">Select Condition</option>
                            <option value="new" {{ old('condition') == 'new' ? 'selected' : '' }}>New</option>
                            <option value="like_new" {{ old('condition') == 'like_new' ? 'selected' : '' }}>Like New</option>
                            <option value="good" {{ old('condition') == 'good' ? 'selected' : '' }}>Good</option>
                            <option value="fair" {{ old('condition') == 'fair' ? 'selected' : '' }}>Fair</option>
                            <option value="poor" {{ old('condition') == 'poor' ? 'selected' : '' }}>Poor</option>
                        </select>
                        @error('condition')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description">Description <span class="required">*</span></label>
                    <textarea id="description" name="description" rows="6" class="form-control @error('description') is-invalid @enderror" required>{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <p class="form-tip">Include details about features, condition, and reason for selling</p>
                </div>
            </div>
            
            <div class="form-section">
                <h3>Photos</h3>
                <p class="section-description">Add up to 5 photos. Clear photos from multiple angles help buyers see your item better.</p>
                
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
            
            <div class="form-section">
                <h3>Contact Information</h3>
                
                <div class="form-group">
                    <label for="location">Location <span class="required">*</span></label>
                    <input type="text" id="location" name="location" value="{{ old('location', Auth::user()->city) }}" class="form-control @error('location') is-invalid @enderror" required>
                    @error('location')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="contact_phone">Phone Number</label>
                    <input type="text" id="contact_phone" name="contact_phone" value="{{ old('contact_phone', Auth::user()->phone) }}" class="form-control @error('contact_phone') is-invalid @enderror">
                    @error('contact_phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group contact-preference">
                    <label>Contact Preferences</label>
                    <div class="checkbox-group">
                        <div class="checkbox-item">
                            <input type="checkbox" name="contact_chat" id="contact_chat" value="1" {{ old('contact_chat') ? 'checked' : 'checked' }}>
                            <label for="contact_chat">Chat</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" name="contact_call" id="contact_call" value="1" {{ old('contact_call') ? 'checked' : '' }}>
                            <label for="contact_call">Phone Calls</label>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="submit-btn">Post Ad</button>
                <a href="{{ route('home') }}" class="cancel-btn">Cancel</a>
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