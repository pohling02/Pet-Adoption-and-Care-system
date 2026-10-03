<!-- In layouts/adopter_master.blade.php -->
<head>
    <!-- Other head elements -->
    <style>
        .hover-shadow {
            transition: all 0.3s ease;
        }

        .hover-shadow:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
        }

        .avatar-sm {
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        /* Loading animation */
        .spinner-border {
            width: 3rem;
            height: 3rem;
        }
    </style>
</head>

@foreach($resources as $resource)
<div class="col-md-6 col-lg-4 mb-4">
    <div class="card h-100 shadow-sm border-0 rounded-4 hover-shadow transition">
        <!-- Resource Image -->
        @if($resource->image_path)
        <div class="position-relative">
            <img src="{{ asset('storage/' . $resource->image_path) }}" alt="{{ $resource->title }}"
                 class="card-img-top rounded-top-4" style="height: 180px; object-fit: cover;">
            <!-- Video Overlay for Video Types -->
            @if($resource->type === 'video')
            <div class="position-absolute top-50 start-50 translate-middle">
                <div class="bg-white rounded-circle p-2 shadow-sm">
                    <i class="fas fa-play text-primary fs-4"></i>
                </div>
            </div>
            @endif
        </div>
        @endif

        <div class="card-body d-flex flex-column">
            <!-- Category Badge -->
            <span class="badge bg-primary bg-opacity-10 text-primary mb-2 rounded-pill px-3 py-2 small">
                <i class="fas fa-tag me-1"></i>{{ ucfirst(str_replace('_', ' ', $resource->category)) }}
            </span>

            <!-- Title -->
            <h5 class="card-title fw-bold mb-3">{{ $resource->title }}</h5>

            <!-- Author with Avatar -->
            @php
            $author = $resource->author;  // The User who created this resource
            $authorName = $author->name ?? 'Unknown';
            $profilePic = null;

            // If the user is shelter staff and has a profile picture, display it.
            if (
            $author 
            && $author->role === 'shelter_staff'
            && $author->shelterStaffProfile
            && !empty($author->shelterStaffProfile->profile_picture)
            ) {
            // Since images for profiles are stored in public/images, we simply use asset()
            $profilePic = $author->shelterStaffProfile->profile_picture;
            }
            @endphp

            <div class="d-flex align-items-center mb-3">
                @if($profilePic)
                <!-- Display Shelter Staff Profile Picture -->
                <img src="{{ asset($profilePic) }}"
                     alt="{{ $authorName }}" 
                     class="avatar-sm rounded-circle me-2">
                @else
                <!-- Fallback: Display first letter of author name -->
                <div class="avatar-sm rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2">
                    {{ strtoupper(substr($authorName, 0, 1)) }}
                </div>
                @endif

                <span class="small text-muted">{{ $authorName }}</span>
                <span class="ms-auto small text-muted">
                    {{ $resource->created_at->format('M d, Y') }}
                </span>
            </div>

            <!-- Content Preview -->
            @if($resource->type !== 'video')
            <p class="card-text text-muted flex-grow-1">
                {!! nl2br(e(Str::limit($resource->content, 100, '...'))) !!}
            </p>
            @endif

            <!-- Action Button -->
            <a href="{{ route('resources.show', $resource->id) }}" class="btn btn-outline-primary btn-sm mt-auto w-100">
                <i class="fas fa-book-reader me-1"></i> Read More
            </a>
        </div>
    </div>
</div>
@endforeach

@if(method_exists($resources, 'links'))
<div class="col-12">
    <div class="d-flex justify-content-center mt-4">
        {{ $resources->links() }}
    </div>
</div>
@endif
