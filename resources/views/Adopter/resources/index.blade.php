@extends('layouts.adopter_master')
@section('title', 'Pet Care Resources')

@section('styles')
<style>
    /* Core layout and design styles */
    .container {
        padding: 3rem 0;
        position: relative;
    }
    .container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%239C92AC' fill-opacity='0.03' fill-rule='evenodd'/%3E%3C/svg%3E");
        pointer-events: none;
        z-index: -1;
    }

    /* Page header styling */
    .page-header {
        position: relative;
        padding: 3rem 0;
        margin-bottom: 3rem;
    }
    .page-header h1 {
        font-size: 2.5rem;
        color: #2c3e50;
        margin-bottom: 1rem;
        letter-spacing: -0.02em;
    }
    .page-header p {
        font-size: 1.2rem;
        color: #5c6b7a;
        max-width: 600px;
        margin: 0 auto;
    }

    /* Featured Resource styling */
    .featured-resource {
        background: linear-gradient(135deg, #f0f7ff 0%, #e6f0fd 100%);
        border-left: 5px solid #4f86f7;
        border-radius: 0.75rem;
        margin-bottom: 2.5rem;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        position: relative;
    }
    .featured-resource:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .featured-resource .image-container {
        position: relative;
        height: 100%;
        overflow: hidden;
    }
    .featured-resource img {
        height: 100%;
        width: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    .featured-resource:hover img {
        transform: scale(1.05);
    }
    .featured-resource .content-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to right, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0) 100%);
    }
    .featured-badge {
        background-color: #4f86f7;
        color: white;
        font-weight: 600;
        letter-spacing: 0.5px;
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
        100% {
            transform: scale(1);
        }
    }
    .featured-cta {
        display: inline-block;
        background-color: #4f86f7;
        color: white !important;
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        transition: all 0.3s ease;
        text-decoration: none;
        margin-top: 1rem;
    }
    .featured-cta:hover {
        background-color: #3a6ad2;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(79, 134, 247, 0.3);
    }

    /* Search box styling */
    .resource-search-box {
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        margin-bottom: 2.5rem;
        position: relative;
        z-index: 10;
        transition: box-shadow 0.3s ease;
    }
    .resource-search-box:hover {
        box-shadow: 0 6px 25px rgba(0,0,0,0.1);
    }
    .search-input {
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }
    .search-input:focus {
        box-shadow: 0 0 0 3px rgba(79, 134, 247, 0.15);
        border-color: #4f86f7;
    }
    .filter-btn {
        display: none;
        transition: all 0.3s ease;
    }
    .filter-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 1rem;
    }
    .filter-tag {
        background-color: #f0f7ff;
        border: 1px solid #e0e9f5;
        color: #4f86f7;
        font-size: 0.8rem;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        transition: all 0.2s ease;
    }
    .filter-tag:hover {
        background-color: #e0e9f5;
    }
    .filter-tag .close {
        margin-left: 0.5rem;
        font-size: 0.75rem;
        cursor: pointer;
    }
    .filter-collapse {
        transition: all 0.3s ease;
    }

    /* Resource Card styling */
    .resource-card {
        border: none;
        border-radius: 16px;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .resource-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    .resource-card .card-img-container {
        height: 200px;
        overflow: hidden;
        position: relative;
    }
    .resource-card img {
        height: 100%;
        width: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    .resource-card:hover img {
        transform: scale(1.08);
    }
    .resource-card .card-body {
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }
    .resource-card .category-badge {
        font-size: 0.8rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
    }
    .resource-card:hover .category-badge {
        background-color: rgba(79, 134, 247, 0.2);
    }
    .resource-meta {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
        font-size: 0.85rem;
    }
    .resource-meta .reading-time {
        display: flex;
        align-items: center;
        margin-left: auto;
    }
    .resource-meta .reading-time i {
        margin-right: 0.25rem;
        color: #7a8896;
    }
    .resource-card .read-more-btn {
        background-color: transparent;
        border: 2px solid #4f86f7;
        color: #4f86f7;
        font-weight: 600;
        padding: 0.5rem 1rem;
        border-radius: 10px;
        transition: all 0.3s ease;
        text-align: center;
        margin-top: auto;
    }
    .resource-card .read-more-btn:hover {
        background-color: #4f86f7;
        color: white;
        transform: translateY(-2px);
    }
    .resource-card .new-badge {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background-color: #ff5252;
        color: white;
        font-size: 0.7rem;
        font-weight: bold;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        z-index: 1;
    }

    /* Content preview */
    .content-preview {
        margin-bottom: 1rem;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        color: #6c757d;
    }
    .content-preview ul {
        margin: 0;
        padding-left: 1.25rem;
    }

    /* Video play button */
    .video-play-btn {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: rgba(255, 255, 255, 0.9);
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    }
    .video-play-btn:hover {
        transform: translate(-50%, -50%) scale(1.1);
        background-color: #fff;
    }
    .video-play-btn i {
        color: #4f86f7;
        font-size: 1.5rem;
        margin-left: 4px;
    }

    /* Video container styling */
    .video-container {
        height: 100%;
        width: 100%;
        overflow: hidden;
    }
    .video-container .ratio {
        height: 100%;
        width: 100%;
    }

    /* Pagination styling */
    .pagination {
        gap: 0.5rem;
    }
    .pagination .page-item .page-link {
        border-radius: 8px;
        border: none;
        color: #4f86f7;
        font-weight: 500;
        padding: 0.5rem 1rem;
        transition: all 0.2s ease;
    }
    .pagination .page-item .page-link:hover {
        background-color: #f0f7ff;
    }
    .pagination .page-item.active .page-link {
        background-color: #4f86f7;
        color: white;
        box-shadow: 0 2px 5px rgba(79, 134, 247, 0.3);
    }

    /* No results message */
    .no-results {
        padding: 3rem;
        border-radius: 12px;
        background: #f8f9fa;
        text-align: center;
        border: 1px dashed #dee2e6;
    }
    .no-results i {
        color: #adb5bd;
    }

    /* Mobile responsiveness */
    @media (max-width: 1199px) {
        .page-header h1 {
            font-size: 2.2rem;
        }
        .page-header p {
            font-size: 1.1rem;
        }
    }

    @media (max-width: 991px) {
        .container {
            padding: 2rem 0;
        }
        .page-header {
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        .resource-card .card-img-container {
            height: 190px;
        }
    }

    @media (max-width: 768px) {
        .container {
            padding: 1.5rem 0;
        }
        .page-header {
            padding: 1.5rem 0;
            margin-bottom: 1.5rem;
        }
        .page-header h1 {
            font-size: 1.8rem;
        }
        .featured-resource {
            margin-bottom: 1.5rem;
        }
        .resource-search-box {
            margin-bottom: 1.5rem;
        }
        .filter-btn {
            display: block;
            width: 100%;
            margin-top: 1rem;
        }
        .filter-collapse {
            margin-top: 1rem;
        }
        .resource-card .card-img-container {
            height: 180px;
        }
    }

    @media (max-width: 576px) {
        .page-header h1 {
            font-size: 1.6rem;
        }
        .page-header p {
            font-size: 1rem;
        }
        .featured-resource .featured-content {
            padding: 1.5rem !important;
        }
        .resource-card .card-img-container {
            height: 160px;
        }
    }
</style>
@endsection

@section('content')
<div class="container">
    <!-- Page Header -->
    <div class="text-center mb-5 page-header">
        <h1 class="fw-bold mb-2">Pet Care Resources</h1>
        <p class="text-muted lead">Discover expert advice and guides to help you care for your furry friend</p>
    </div>

    <!-- Featured Resource -->
    <div class="featured-resource mb-5 shadow">
        <div class="row g-0">
            <div class="col-lg-5 col-md-6">
                <div class="image-container">
                    <div class="content-overlay"></div>
                    <img src="{{ asset('images/resources.jpg') }}" alt="Featured Resource" class="img-fluid w-100">
                </div>
            </div>
            <div class="col-lg-7 col-md-6 p-4 p-md-5 featured-content">
                <span class="badge featured-badge rounded-pill mb-3 px-3 py-2">Featured</span>
                <h3 class="fw-bold mb-3">Essential Pet Care Guide</h3>
                <p class="text-muted mb-4">
                    Learn everything you need to know about caring for your new pet companion with our comprehensive guide.
                    From nutrition to exercise, health checkups to training tips, we've got you covered.
                </p>
            </div>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="card resource-search-box border-0 mb-5">
        <div class="card-body p-4">
            <form id="resource-filter-form" action="{{ route('adopter.resources') }}" method="GET">
                <div class="row g-3">
                    <!-- Search Input -->
                    <div class="col-lg-6 col-md-12">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" class="form-control search-input border-start-0" 
                                   placeholder="Search resources..." id="resource-search" name="search"
                                   value="{{ request('search') }}">
                        </div>
                    </div>

                    <!-- Mobile Filter Toggle Button -->
                    <div class="col-12 d-md-none">
                        <button type="button" class="btn btn-outline-secondary filter-btn" 
                                data-bs-toggle="collapse" data-bs-target="#filter-collapse">
                            <i class="fas fa-filter me-2"></i> 
                            {{ request('category') || request('type') ? 'Filters Applied' : 'Show Filters' }}
                        </button>
                    </div>

                    <!-- Filter Collapse for Mobile -->
                    <div class="col-12 collapse d-md-block" id="filter-collapse">
                        <div class="row g-3 filter-collapse">
                            <!-- Category Filter -->
                            <div class="col-md-6 col-lg-3">
                                <label for="category-filter" class="form-label small fw-bold">Category</label>
                                <select class="form-select search-input" id="category-filter" name="category">
                                    <option value="">All Categories</option>
                                    <option value="pet_care" {{ request('category') == 'pet_care' ? 'selected' : '' }}>Pet Care</option>
                                    <option value="food_safety" {{ request('category') == 'food_safety' ? 'selected' : '' }}>Food Safety</option>
                                    <option value="training" {{ request('category') == 'training' ? 'selected' : '' }}>Training</option>
                                </select>
                            </div>
                            <!-- Type Filter -->
                            <div class="col-md-6 col-lg-3">
                                <label for="type-filter" class="form-label small fw-bold">Type</label>
                                <select class="form-select search-input" id="type-filter" name="type">
                                    <option value="">All Types</option>
                                    <option value="article" {{ request('type') == 'article' ? 'selected' : '' }}>Articles</option>
                                    <option value="video" {{ request('type') == 'video' ? 'selected' : '' }}>Videos</option>
                                </select>
                            </div>
                            <!-- Apply and Reset Buttons -->
                            <div class="col-md-12 col-lg-6 d-flex align-items-end">
                                <div class="d-flex gap-2 w-100">
                                    <button type="submit" class="btn btn-primary flex-grow-1">
                                        <i class="fas fa-search me-2"></i> Apply Filters
                                    </button>
                                    <a href="{{ route('adopter.resources') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-redo me-2"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Applied Filters Tags -->
                @if(request('search') || request('category') || request('type'))
                <div class="filter-tags mt-3">
                    @if(request('search'))
                    <div class="filter-tag">
                        <span>Search: "{{ request('search') }}"</span>
                        <a href="{{ route('adopter.resources', array_merge(request()->except('search'), ['page' => 1])) }}" class="close ms-2 text-decoration-none">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                    @endif

                    @if(request('category'))
                    <div class="filter-tag">
                        <span>Category: {{ ucfirst(str_replace('_', ' ', request('category'))) }}</span>
                        <a href="{{ route('adopter.resources', array_merge(request()->except('category'), ['page' => 1])) }}" class="close ms-2 text-decoration-none">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                    @endif

                    @if(request('type'))
                    <div class="filter-tag">
                        <span>Type: {{ ucfirst(request('type')) }}</span>
                        <a href="{{ route('adopter.resources', array_merge(request()->except('type'), ['page' => 1])) }}" class="close ms-2 text-decoration-none">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                    @endif
                </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Resources Grid -->
    <div class="row g-4" id="resources-container">
        @if($resources->count() > 0)
        @foreach($resources as $resource)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card resource-card h-100 shadow-sm border-0">
                <div class="card-body">
                    <!-- Category Badge with appropriate icon -->
                    <span class="badge bg-primary bg-opacity-10 text-primary mb-2 rounded-pill px-3 py-2 small category-badge">
                        <i class="fas fa-{{ $resource->category == 'pet_care' ? 'paw' : ($resource->category == 'food_safety' ? 'utensils' : 'graduation-cap') }} me-1"></i>
                        {{ ucfirst(str_replace('_', ' ', $resource->category)) }}
                    </span>

                    <!-- Title -->
                    <h5 class="card-title fw-bold mb-3">{{ $resource->title }}</h5>

                    <!-- Meta Information -->
                    <div class="resource-meta">
                        <span class="author">
                            <i class="fas fa-user-edit me-1 text-muted"></i>
                            {{ $resource->author->name ?? 'Unknown' }}
                        </span>
                    </div>

                    <!-- Image/Video Container -->
                    <div class="card-img-container">
                        @if($resource->type === 'video')
                        <!-- Video Content -->
                        <div class="video-container">
                            <div class="ratio ratio-16x9">
                                {!! $resource->content !!}
                            </div>
                        </div>
                        @else
                        <!-- Image Content -->
                        @php
                        $imagePaths = $resource->image_paths ? json_decode($resource->image_paths, true) : [];
                        @endphp

                        @if(!empty($imagePaths))
                        <img 
                            src="{{ asset('storage/' . $imagePaths[0]) }}" 
                            alt="{{ $resource->title }}"
                            class="card-img-top">
                        @else
                        <img
                            src="{{ asset('images/no-image.png') }}"
                            alt="No Image"
                            class="card-img-top">
                        @endif
                        @endif
                    </div>

                    <!-- Content Preview -->
                    @if($resource->type !== 'video')
                    <!-- Display a short text preview for articles -->
                    <div class="content-preview">
                        @if(strpos($resource->content, '- ') !== false)
                        <!-- Display bullet points if found -->
                        <ul>
                            @foreach(array_slice(explode('- ', $resource->content), 1, 3) as $point)
                            <li>{{ Str::limit(trim($point), 60) }}</li>
                            @endforeach
                        </ul>
                        @else
                        {!! nl2br(e(Str::limit($resource->content, 120, '...'))) !!}
                        @endif
                    </div>
                    @else
                    <!-- For video, show a short description -->
                    <p class="content-preview">
                        Video tutorial: {{ Str::limit($resource->description ?? 'Watch this helpful video guide', 100) }}
                    </p>
                    @endif

                    <!-- Publication Date -->
                    <div class="text-muted small mb-3">
                        <i class="far fa-calendar-alt me-1"></i>
                        {{ $resource->created_at->format('M d, Y') }}
                    </div>

                    <!-- Action Button -->
                    <a href="{{ route('resources.show', $resource->id) }}" class="btn read-more-btn w-100">
                        <i class="fas fa-{{ $resource->type === 'video' ? 'play-circle' : 'book-open' }} me-1"></i>
                        {{ $resource->type === 'video' ? 'Watch Now' : 'Read More' }}
                    </a>
                </div>
            </div>
        </div>
        @endforeach

        <!-- Pagination -->
        @if(method_exists($resources, 'links'))
        <div class="col-12">
            <div class="d-flex justify-content-center mt-5">
                {{ $resources->appends(request()->query())->links() }}
            </div>
        </div>
        @endif
        @else
        <!-- No Results Message -->
        <div class="col-12 text-center py-5">
            <div class="no-results">
                <i class="fas fa-search fa-3x text-muted mb-4"></i>
                <h4 class="mb-3">No resources found</h4>
                <p class="text-muted mb-4">Try adjusting your search or filters</p>
                <a href="{{ route('adopter.resources') }}" class="btn btn-outline-primary px-4 py-2">
                    <i class="fas fa-redo me-2"></i> Reset Filters
                </a>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filterForm = document.getElementById('resource-filter-form');
        const searchInput = document.getElementById('resource-search');
        const filterButton = document.querySelector('.filter-btn');

        // Update filter button text and style based on whether filters are applied
        const categoryFilter = document.getElementById('category-filter');
        const typeFilter = document.getElementById('type-filter');

        if (categoryFilter.value || typeFilter.value || searchInput.value) {
            filterButton.innerHTML = '<i class="fas fa-filter me-2"></i> Filters Applied';
            filterButton.classList.add('btn-primary');
            filterButton.classList.remove('btn-outline-secondary');
        }

        // Update search input appearance on focus
        searchInput.addEventListener('focus', function () {
            this.parentElement.classList.add('shadow-sm');
        });
        searchInput.addEventListener('blur', function () {
            this.parentElement.classList.remove('shadow-sm');
        });

        // Animation for filter collapse
        const filterCollapse = document.getElementById('filter-collapse');
        filterCollapse.addEventListener('show.bs.collapse', function () {
            this.style.opacity = '0';
            setTimeout(() => {
                this.style.opacity = '1';
            }, 50);
        });

        // Auto-submit form when changing select filters
        const selectFilters = document.querySelectorAll('select.search-input');
        selectFilters.forEach(select => {
            select.addEventListener('change', function () {
                if (window.innerWidth > 768) {  // Only auto-submit on larger screens
                    filterForm.submit();
                }
            });
        });

        // Add smooth hover effects on cards
        const resourceCards = document.querySelectorAll('.resource-card');
        resourceCards.forEach(card => {
            card.addEventListener('mouseenter', function () {
                this.style.transform = 'translateY(-5px)';
                this.style.boxShadow = '0 10px 25px rgba(0,0,0,0.1)';
            });
            card.addEventListener('mouseleave', function () {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '0 0.125rem 0.25rem rgba(0, 0, 0, 0.075)';
            });
        });
    });
</script>
@endsection