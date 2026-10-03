@extends('layouts.shelter_master')

@section('title', 'Edit Pet Resource')

@section('content')
<div class="container py-5">
    <div class="card shadow-lg border-0 rounded-4 mx-auto" style="max-width: 800px;">
        <div class="card-body p-4">
            <h2 class="text-center text-primary fw-bold">
                <i class="fas fa-edit"></i> Edit Pet Resource
            </h2>

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <form action="{{ route('shelter.resources.update', $resource->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Title -->
                <div class="mb-3">
                    <label for="title" class="form-label fw-semibold">Resource Title</label>
                    <input type="text" name="title" id="title"
                           class="form-control @error('title') is-invalid @enderror"
                           value="{{ old('title', $resource->title) }}" required>
                    @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Type (Article / Video) -->
                <div class="mb-3">
                    <label for="type" class="form-label fw-semibold">Resource Type</label>
                    <select name="type" id="type" class="form-select @error('type') is-invalid @enderror">
                        <option value="article" {{ $resource->type == 'article' ? 'selected' : '' }}>Article</option>
                        <option value="video" {{ $resource->type == 'video' ? 'selected' : '' }}>Video</option>
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
                              rows="5">{{ old('content', $resource->content) }}</textarea>
                    @error('content')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Category -->
                <div class="mb-3">
                    <label for="category" class="form-label fw-semibold">Category</label>
                    <select name="category" id="category" class="form-select @error('category') is-invalid @enderror">
                        <option value="pet_care" {{ $resource->category == 'pet_care' ? 'selected' : '' }}>Pet Care Tips</option>
                        <option value="food_safety" {{ $resource->category == 'food_safety' ? 'selected' : '' }}>Foods to Avoid</option>
                        <option value="training" {{ $resource->category == 'training' ? 'selected' : '' }}>Pet Training</option>
                    </select>
                    @error('category')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Stored Images Preview with Delete Option -->
                @if($resource->image_paths)
                    @php
                        // Ensure we have an array of images.
                        $storedImages = is_array($resource->image_paths) ? $resource->image_paths : json_decode($resource->image_paths, true);
                    @endphp

                    @if(!empty($storedImages))
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Current Images</label>
                        <div id="storedImages" class="d-flex flex-wrap">
                            @foreach($storedImages as $image)
                            <div class="position-relative me-2 mb-2">
                                <img src="{{ asset('storage/' . $image) }}" alt="Resource Image" class="img-thumbnail" style="max-width: 150px; max-height: 150px;">
                                <!-- Delete button for each image -->
                                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 delete-stored-image" data-image="{{ $image }}" style="transform: translate(50%, -50%);">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                @endif

                <!-- Container for hidden inputs marking images for deletion -->
                <div id="deletedImagesContainer"></div>

                <!-- New Image Upload with Preview -->
                <div class="mb-3">
                    <label for="image" class="form-label fw-semibold">Upload New Image (Optional)</label>
                    <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror" accept="image/*" onchange="previewNewImage(this)">
                    @error('image')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    
                    <!-- New image preview container -->
                    <div id="newImagePreview" class="mt-2 d-none">
                        <div class="position-relative d-inline-block">
                            <img id="imagePreview" src="#" alt="New Image Preview" class="img-thumbnail" style="max-width: 150px; max-height: 150px;">
                            <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0" onclick="clearNewImage()" style="transform: translate(50%, -50%);">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('shelter.resources.manage') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Update Resource
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript for handling image operations -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Attach event listeners to all delete buttons for stored images
        document.querySelectorAll('.delete-stored-image').forEach(button => {
            button.addEventListener('click', function () {
                const image = this.getAttribute('data-image');
                // Create a hidden input to flag this image for deletion
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'delete_images[]';
                input.value = image;
                document.getElementById('deletedImagesContainer').appendChild(input);
                
                // Remove the image preview element from the DOM
                this.parentElement.remove();
            });
        });
    });

    // Preview new image when selected
    function previewNewImage(input) {
        const previewContainer = document.getElementById('newImagePreview');
        const preview = document.getElementById('imagePreview');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                previewContainer.classList.remove('d-none');
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Clear new image selection
    function clearNewImage() {
        const fileInput = document.getElementById('image');
        const previewContainer = document.getElementById('newImagePreview');
        
        fileInput.value = ''; // Clear the file input
        previewContainer.classList.add('d-none'); // Hide the preview
    }
</script>
@endsection