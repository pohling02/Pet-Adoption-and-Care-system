@extends('layouts.shelter_master')

@section('title', 'Manage Pet Resources')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary fw-bold">
            <i class="fas fa-book me-2"></i> Manage Pet Resources
        </h2>
        <!-- Button that directly redirects to the create page -->
        <a href="{{ route('shelter.resources.create') }}" class="btn btn-success px-3 py-2">
            <i class="fas fa-plus me-2"></i> Add New Resource
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-check-circle me-2"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <select id="filter-type" class="form-select">
                        <option value="">All Types</option>
                        <option value="article">Article</option>
                        <option value="video">Video</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <select id="filter-category" class="form-select">
                        <option value="">All Categories</option>
                        <option value="pet_care">Pet Care Tips</option>
                        <option value="food_safety">Foods to Avoid</option>
                        <option value="training">Pet Training</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" class="form-control" id="search-resources" placeholder="Search resources...">
                        <button class="btn btn-outline-primary" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="ps-3">#</th>
                            <th>Image</th> <!-- Add Image Column -->
                            <th>Title</th>
                            <th>Type</th>
                            <th>Category</th>
                            <th>Created On</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="resources-table-body">
                        @forelse ($resources as $resource)
                        <tr>
                            <td class="ps-3">{{ $loop->iteration }}</td>
                            <td>
                                @php
                                $imagePaths = $resource->image_paths ? json_decode($resource->image_paths, true) : [];
                                @endphp

                                @if(!empty($imagePaths))
                                @foreach($imagePaths as $imagePath)
                                <img src="{{ asset('storage/' . $imagePath) }}" alt="{{ $resource->title }}" 
                                     class="img-fluid me-1" style="max-width: 80px;">
                                @endforeach
                                @else
                                <span class="text-muted">No Image</span>
                                @endif
                            </td>

                            <td>
                                <div class="fw-medium">{{ $resource->title }}</div>
                                <small class="text-muted">{{ Str::limit($resource->description, 60) }}</small>
                            </td>
                            <td>
                                @if($resource->type == 'article')
                                <span class="badge bg-info text-dark rounded-pill px-3 py-2">
                                    <i class="fas fa-file-alt me-1"></i> Article
                                </span>
                                @elseif($resource->type == 'video')
                                <span class="badge bg-danger rounded-pill px-3 py-2">
                                    <i class="fas fa-video me-1"></i> Video
                                </span>
                                @endif
                            </td>
                            <td>
                                @if($resource->category == 'pet_care')
                                <span class="badge bg-success rounded-pill px-3 py-2">
                                    <i class="fas fa-paw me-1"></i> Pet Care
                                </span>
                                @elseif($resource->category == 'food_safety')
                                <span class="badge bg-warning text-dark rounded-pill px-3 py-2">
                                    <i class="fas fa-utensils me-1"></i> Foods to Avoid
                                </span>
                                @elseif($resource->category == 'training')
                                <span class="badge bg-primary rounded-pill px-3 py-2">
                                    <i class="fas fa-graduation-cap me-1"></i> Training
                                </span>
                                @endif
                            </td>
                            <td>{{ $resource->created_at->format('M d, Y') }}</td>
                            <td class="text-end pe-3">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('shelter.resources.edit', $resource->id) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </a>
                                    <button type="button" class="btn btn-outline-danger btn-sm delete-resource" 
                                            data-resource-id="{{ $resource->id }}"
                                            data-resource-title="{{ $resource->title }}">
                                        <i class="fas fa-trash-alt me-1"></i> Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-folder-open fa-3x mb-3"></i>
                                    <h5>No resources found</h5>
                                    <p>Get started by adding your first pet resource</p>
                                    <a href="{{ route('shelter.resources.create') }}" class="btn btn-primary mt-2">
                                        <i class="fas fa-plus me-2"></i> Add New Resource
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Showing <span class="fw-medium">{{ $resources->firstItem() ?? 0 }}</span> to 
                    <span class="fw-medium">{{ $resources->lastItem() ?? 0 }}</span> of 
                    <span class="fw-medium">{{ $resources->total() }}</span> resources
                </div>
                {{ $resources->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<!-- Delete Form -->
<form id="delete-resource-form" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteResourceModal" tabindex="-1" aria-labelledby="deleteResourceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteResourceModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i> Delete Resource
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="delete-resource-title"></strong>?</p>
                <p class="text-muted mb-0">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirm-delete" class="btn btn-danger">
                    <i class="fas fa-trash-alt me-1"></i> Delete Resource
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteResourceModal'));
        const deleteForm = document.getElementById('delete-resource-form');

        document.querySelectorAll(".delete-resource").forEach(button => {
            button.addEventListener("click", function () {
                const resourceId = this.getAttribute("data-resource-id");
                const resourceTitle = this.getAttribute("data-resource-title");

                deleteForm.action = `${window.location.origin}/shelter/resources/${resourceId}`;
                document.getElementById("delete-resource-title").textContent = resourceTitle;

                deleteModal.show();
            });
        });

        document.getElementById("confirm-delete").addEventListener("click", function () {
            deleteForm.submit();
            deleteModal.hide();
        });

        const filterType = document.getElementById('filter-type');
        const filterCategory = document.getElementById('filter-category');
        const searchResources = document.getElementById('search-resources');

        filterType.addEventListener('change', filterResources);
        filterCategory.addEventListener('change', filterResources);
        searchResources.addEventListener('input', filterResources);

        function filterResources() {
            const typeFilter = filterType.value.toLowerCase();
            const categoryFilter = filterCategory.value.toLowerCase();
            const searchTerm = searchResources.value.toLowerCase();

            const rows = document.querySelectorAll('#resources-table-body tr:not(.empty-message)');
            let visibleCount = 0;

            rows.forEach(row => {
                const title = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                const type = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
                const category = row.querySelector('td:nth-child(5)').textContent.toLowerCase();

                const typeMatch = !typeFilter || type.includes(typeFilter);
                const categoryMatch = !categoryFilter || category.includes(categoryFilter);
                const searchMatch = !searchTerm || title.includes(searchTerm);

                const shouldShow = typeMatch && categoryMatch && searchMatch;
                row.style.display = shouldShow ? '' : 'none';

                if (shouldShow)
                    visibleCount++;
            });

            const emptyRow = document.querySelector('.empty-message');
            if (visibleCount === 0 && !emptyRow) {
                const tableBody = document.getElementById('resources-table-body');
                const newRow = document.createElement('tr');
                newRow.className = 'empty-message';
                newRow.innerHTML = `
                    <td colspan="7" class="text-center py-5">
                        <div class="text-muted">
                            <i class="fas fa-search fa-3x mb-3"></i>
                            <h5>No matching resources found</h5>
                            <p>Try adjusting your filters to find what you're looking for</p>
                            <button type="button" class="btn btn-outline-secondary mt-2" id="clear-filters">
                                <i class="fas fa-times me-2"></i> Clear Filters
                            </button>
                        </div>
                    </td>
                `;
                tableBody.appendChild(newRow);

                document.getElementById('clear-filters').addEventListener('click', () => {
                    filterType.value = '';
                    filterCategory.value = '';
                    searchResources.value = '';
                    filterResources();
                });
            } else if (visibleCount > 0 && emptyRow) {
                emptyRow.remove();
            }
        }
    });
</script>
@endsection
