@extends('layouts.adopter_master')
@section('title', $resource->title)

@section('content')
<div class="container py-5">
    <div class="card shadow-lg border-0 rounded-4 mx-auto" style="max-width: 900px;">
        <div class="card-body p-4 p-md-5"> <!-- Increased padding -->
            
            <!-- Header with icon -->
            <div class="mb-4">
                <h2 class="fw-bold text-primary text-center d-flex align-items-center justify-content-center">
                    <i class="fas fa-paw me-2"></i> {{ $resource->title }}
                </h2>
                <!-- Author and Category with improved styling -->
                <div class="d-flex justify-content-center align-items-center gap-2 mb-3">
                    <span class="badge bg-primary text-white px-3 py-2 rounded-pill">
                        {{ ucfirst(str_replace('_', ' ', $resource->category)) }}
                    </span>
                    <span class="text-muted">|</span>
                    <span><strong>Author:</strong> {{ $resource->author->name ?? 'Unknown' }}</span>
                </div>
            </div>
            
            <!-- Images Section -->
            @php
                // If you store multiple images as JSON (e.g. ["cat1.jpg","cat2.png"]):
                $imagePaths = $resource->image_paths 
                    ? json_decode($resource->image_paths, true) 
                    : [];

                // If you only store a single path as plain text (e.g. "cat1.jpg"),
                // you can do:
                // $imagePaths = $resource->image_paths ? [$resource->image_paths] : [];
            @endphp

            @if(!empty($imagePaths))
                <!-- Loop over all images or just show the first -->
                @foreach($imagePaths as $img)
                    <div class="text-center mb-4">
                        <img 
                            src="{{ asset('storage/' . $img) }}" 
                            alt="Resource Image" 
                            class="img-fluid rounded shadow-sm"
                            style="max-height: 400px; object-fit: cover;"
                        >
                    </div>
                @endforeach
            @endif
            
            <!-- Content Section -->
            <div class="resource-content">
                @if($resource->type === 'video')
                    <!-- If 'content' holds an <iframe> or embed code -->
                    <div class="ratio ratio-16x9 mb-4 shadow-sm">
                        {!! $resource->content !!}
                    </div>
                @else
                    <!-- For articles, show text -->
                    <div class="lead lh-base" style="text-align: justify;">
                        {!! nl2br(e($resource->content)) !!}
                    </div>
                @endif
            </div>
            
            <!-- Action Buttons -->
            <div class="d-flex justify-content-center gap-3 mt-5">
                <a href="{{ route('adopter.resources') }}" class="btn btn-outline-secondary px-4">
                    <i class="fas fa-arrow-left me-2"></i> Back to Resources
                </a>
            </div>
            
            <!-- Related Resources Section -->
            @if(isset($relatedResources) && count($relatedResources) > 0)
                <div class="mt-5">
                    <h5 class="border-bottom pb-2">Related Resources</h5>
                    <div class="row row-cols-1 row-cols-md-2 g-4 mt-2">
                        @foreach($relatedResources as $related)
                            <div class="col">
                                <a href="{{ route('adopter.resource.show', $related->id) }}" class="text-decoration-none">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body">
                                            <h6 class="card-title text-primary">{{ $related->title }}</h6>
                                            <p class="card-text small text-muted">
                                                {{ Str::limit(strip_tags($related->content), 80) }}
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
@endsection
