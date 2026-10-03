@extends('layouts.shelter_master')

@section('title', 'Add Pet Resource')

@section('content')
<div class="container py-5">
    <div class="card shadow-lg border-0 rounded-4 mx-auto" style="max-width: 800px;">
        <div class="card-body p-4">
            <h2 class="text-center text-primary fw-bold">
                <i class="fas fa-book-open"></i> Add Pet Resource
            </h2>

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <form action="{{ route('shelter.resources.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Title -->
                <div class="mb-3">
                    <label for="title" class="form-label fw-semibold">Resource Title</label>
                    <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror"
                           placeholder="Enter resource title" value="{{ old('title') }}" required>
                    @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Type (Article / Video) -->
                <div class="mb-3">
                    <label for="type" class="form-label fw-semibold">Resource Type</label>
                    <select name="type" id="type" class="form-select @error('type') is-invalid @enderror">
                        <option value="" disabled selected>Select Resource Type</option>
                        <option value="article">Article</option>
                        <option value="video">Video</option>
                    </select>
                    @error('type')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Content / URL -->
                <div class="mb-3">
                    <label for="content" class="form-label fw-semibold">Resource Content / URL</label>
                    <textarea name="content" id="content"
                              class="form-control @error('content') is-invalid @enderror"
                              rows="5" placeholder="Enter article content or video URL">{{ old('content') }}</textarea>
                    @error('content')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Category -->
                <div class="mb-3">
                    <label for="category" class="form-label fw-semibold">Category</label>
                    <select name="category" id="category" class="form-select @error('category') is-invalid @enderror">
                        <option value="" disabled selected>Select Category</option>
                        <option value="pet_care">Pet Care Tips</option>
                        <option value="food_safety">Foods to Avoid</option>
                        <option value="training">Pet Training</option>
                    </select>
                    @error('category')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Image Upload -->
                <div class="mb-3">
                    <label for="image" class="form-label fw-semibold">Upload Images (Optional)</label>
                    <input type="file" name="image[]" id="image"
                           class="form-control @error('image') is-invalid @enderror" accept="image/*" multiple>
                    @error('image')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Image Preview Container -->
                <div id="imagePreview" class="d-flex flex-wrap mb-3">
                    <!-- Image thumbnails with delete buttons will appear here -->
                </div>

                <!-- Buttons -->
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('shelter.resources.manage') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Add Resource
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript for Image Preview with Delete Option -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const imageInput = document.getElementById('image');
        const previewContainer = document.getElementById('imagePreview');

        // Create a DataTransfer object to manage the file list
        let dt = new DataTransfer();

        // When files are selected, add them to the DataTransfer object
        imageInput.addEventListener('change', function(event) {
            // Add each new file to dt
            for (let file of event.target.files) {
                dt.items.add(file);
            }
            // Update the file input's FileList with the DataTransfer files
            imageInput.files = dt.files;
            updatePreviews();
        });

        // Function to update image previews based on current file list
        function updatePreviews() {
            previewContainer.innerHTML = '';

            Array.from(imageInput.files).forEach((file, index) => {
                if (!file.type.startsWith('image/')) return;

                const reader = new FileReader();
                reader.onload = function(e) {
                    // Create a wrapper div for each preview
                    const previewDiv = document.createElement('div');
                    previewDiv.classList.add('position-relative', 'me-2', 'mb-2');

                    // Create the image element
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = 'Image Preview';
                    img.className = 'img-thumbnail';
                    img.style.maxWidth = '150px';
                    img.style.maxHeight = '150px';

                    // Create a delete button
                    const deleteBtn = document.createElement('button');
                    deleteBtn.type = 'button';
                    deleteBtn.className = 'btn btn-sm btn-danger position-absolute top-0 end-0';
                    deleteBtn.style.transform = 'translate(50%, -50%)';
                    deleteBtn.innerHTML = '&times;';
                    deleteBtn.addEventListener('click', () => {
                        removeFile(index);
                    });

                    // Append image and button to the wrapper
                    previewDiv.appendChild(img);
                    previewDiv.appendChild(deleteBtn);
                    previewContainer.appendChild(previewDiv);
                };
                reader.readAsDataURL(file);
            });
        }

        // Function to remove a file from the DataTransfer object at a given index
        function removeFile(index) {
            const dtNew = new DataTransfer();
            Array.from(dt.files).forEach((file, i) => {
                if (i !== index) {
                    dtNew.items.add(file);
                }
            });
            dt = dtNew;
            imageInput.files = dt.files;
            updatePreviews();
        }
    });
</script>
@endsection
