@extends('admin.layouts.app')

@section('title', 'Edit Official Product')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Edit Official Product</h1>
    <a href="{{ route('admin.official-products.index') }}" class="btn btn-outline-secondary">Back</a>
</div>

@if ($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

<div class="card">
  <div class="card-body">
    <form action="{{ route('admin.official-products.update', $product->productId) }}" method="POST" enctype="multipart/form-data" class="row g-3">
      @csrf
      @method('PUT')
      <div class="col-md-6">
        <label class="form-label">Name</label>
        <input type="text" name="name" value="{{ old('name', $product->name) }}" class="form-control @error('name') is-invalid @enderror" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-6">
        <label class="form-label">Category</label>
        <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
          <option value="">-- Select --</option>
          @foreach($categories as $category)
            <option value="{{ $category->categoryId }}" {{ old('category_id', $product->categoryId) == $category->categoryId ? 'selected' : '' }}>{{ $category->name }}</option>
          @endforeach
        </select>
        @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>

      <div class="col-md-6">
        <label class="form-label">Price (₹)</label>
        <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $product->price) }}" class="form-control @error('price') is-invalid @enderror" required>
        @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>

      <div class="col-md-6">
        <label class="form-label">Condition</label>
        <select name="condition" class="form-select @error('condition') is-invalid @enderror" required>
          @php($cond = old('condition', $product->condition))
          <option value="">Select Condition</option>
          <option value="new" {{ $cond == 'new' ? 'selected' : '' }}>New</option>
          <option value="like_new" {{ $cond == 'like_new' ? 'selected' : '' }}>Like New</option>
          <option value="good" {{ $cond == 'good' ? 'selected' : '' }}>Good</option>
          <option value="fair" {{ $cond == 'fair' ? 'selected' : '' }}>Fair</option>
          <option value="poor" {{ $cond == 'poor' ? 'selected' : '' }}>Poor</option>
        </select>
        @error('condition')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>

      <div class="col-12">
        <label class="form-label">Description</label>
        <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $product->description) }}</textarea>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>

      <div class="col-12">
        <label class="form-label">Existing Photos</label>
        <div class="d-flex flex-wrap gap-2">
          @if($product->image)
            <img src="{{ asset('storage/' . $product->image) }}" alt="Main Image" class="img-thumbnail" style="width:96px;height:96px;object-fit:cover;">
          @endif
          @foreach($product->images as $img)
            <img src="{{ asset('storage/' . $img->image_path) }}" alt="Image" class="img-thumbnail" style="width:96px;height:96px;object-fit:cover;">
          @endforeach
        </div>
      </div>

      <div class="col-12">
        <label class="form-label">Add Photos</label>
        <div class="upload-container">
          <div class="upload-area" id="upload-area">
            <input type="file" id="images" name="images[]" accept="image/*" multiple class="file-input @error('images') is-invalid @enderror @error('images.*') is-invalid @enderror">
            <div class="upload-message">
              <i class="fas fa-cloud-upload-alt"></i>
              <p>Drag and drop photos or <span>Browse</span></p>
              <small>(Max 5 photos, up to 2MB each)</small>
            </div>
          </div>
          <div class="preview-container" id="preview-container"></div>
          @error('images')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          @error('images.*')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
      </div>

      <div class="col-12 d-flex gap-2">
        <button class="btn btn-primary" type="submit">Save Changes</button>
        <a href="{{ route('admin.official-products.index') }}" class="btn btn-outline-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>

@section('styles')
<style>
  .upload-container { display: flex; flex-direction: column; gap: 12px; }
  .upload-area { position: relative; border: 2px dashed #cfd8dc; border-radius: 8px; padding: 24px; text-align: center; background: #fafbfd; }
  .upload-area.highlight { border-color: #18d6c7; background: #f0fffd; }
  .upload-area .file-input { position: absolute; inset: 0; opacity: 0; cursor: pointer; }
  .upload-message { color: #607d8b; }
  .upload-message i { font-size: 28px; color: #90a4ae; margin-bottom: 6px; }
  .upload-message span { color: #18d6c7; font-weight: 600; text-decoration: underline; }
  .preview-container { display: flex; flex-wrap: wrap; gap: 12px; }
  .preview-item { position: relative; width: 110px; height: 110px; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden; background: #fff; box-shadow: 0 1px 2px rgba(0,0,0,0.04); }
  .preview-item img { width: 100%; height: 100%; object-fit: cover; }
  .preview-item .remove-btn { position: absolute; top: 6px; right: 6px; width: 28px; height: 28px; border: none; border-radius: 50%; background: rgba(0,0,0,0.55); color: #fff; display: inline-flex; align-items: center; justify-content: center; }
  .preview-item .remove-btn i { font-size: 12px; }
</style>
@endsection

@section('scripts')
<script>
  const fileInput = document.getElementById('images');
  const previewContainer = document.getElementById('preview-container');
  const uploadArea = document.getElementById('upload-area');

  fileInput.addEventListener('change', function() {
    previewContainer.innerHTML = '';
    if (this.files) {
      [...this.files].slice(0, 5).forEach(file => {
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
          removeBtn.onclick = function(ev) {
            ev.preventDefault();
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

  ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    uploadArea.addEventListener(eventName, preventDefaults, false);
  });
  function preventDefaults(e) { e.preventDefault(); e.stopPropagation(); }
  ['dragenter', 'dragover'].forEach(eventName => { uploadArea.addEventListener(eventName, highlight, false); });
  ['dragleave', 'drop'].forEach(eventName => { uploadArea.addEventListener(eventName, unhighlight, false); });
  function highlight() { uploadArea.classList.add('highlight'); }
  function unhighlight() { uploadArea.classList.remove('highlight'); }
  uploadArea.addEventListener('drop', handleDrop, false);
  function handleDrop(e) {
    const dt = e.dataTransfer; const files = dt.files; fileInput.files = files;
    const event = new Event('change', { bubbles: true }); fileInput.dispatchEvent(event);
  }
</script>
@endsection
@endsection
