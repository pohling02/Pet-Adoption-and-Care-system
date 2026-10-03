@extends('layouts.adopter_master')

@section('content')
<div class="container">
    <div class="text-center p-5 rounded" style="background: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.3)), url('{{ asset('images/banner-pets.jpg') }}') no-repeat center/cover; background-color: #2c4b7c;">
        <h2 class="fw-bold text-white">Discover their smiles, hear their stories, and give a pet the loving home they've been waiting for.</h2>
    </div>

    <!-- Improved Search & Filter Section -->
    <div class="row mt-4">
        <div class="col-12 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div class="search-box w-75">
                    <input type="text" id="search-input" class="form-control" placeholder="Search your pet here">
                </div>
                <div class="sort-options">
                    <select class="form-select" id="sort-select">
                        <option value="newest">Newest First</option>
                        <option value="oldest">Oldest First</option>
                        <option value="name-asc">Name (A-Z)</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="col-md-3">
            <div class="filter-section p-3 bg-light rounded">
                <div class="d-flex justify-content-between mb-3">
                    <h5 class="mb-0">Filters</h5>
                    <button id="clear-filters" class="btn btn-sm btn-outline-secondary">Clear All</button>
                </div>

                <h6>Available State</h6>
                <ul class="list-group border-0">
                    <li class="list-group-item border-0 bg-transparent"><input type="checkbox" class="filter-state" value="Johor"> Johor</li>
                    <li class="list-group-item border-0 bg-transparent"><input type="checkbox" class="filter-state" value="Kedah"> Kedah</li>
                    <li class="list-group-item border-0 bg-transparent"><input type="checkbox" class="filter-state" value="Kelantan"> Kelantan</li>
                    <li class="list-group-item border-0 bg-transparent"><input type="checkbox" class="filter-state" value="Melaka"> Melaka</li>
                    <li class="list-group-item border-0 bg-transparent"><input type="checkbox" class="filter-state" value="Negeri Sembilan"> Negeri Sembilan</li>
                    <li class="list-group-item border-0 bg-transparent"><input type="checkbox" class="filter-state" value="Pahang"> Pahang</li>
                    <li class="list-group-item border-0 bg-transparent"><input type="checkbox" class="filter-state" value="Perak"> Perak</li>
                    <li class="list-group-item border-0 bg-transparent"><input type="checkbox" class="filter-state" value="Perlis"> Perlis</li>
                    <li class="list-group-item border-0 bg-transparent"><input type="checkbox" class="filter-state" value="Penang"> Penang</li>
                    <li class="list-group-item border-0 bg-transparent"><input type="checkbox" class="filter-state" value="Selangor"> Selangor</li>
                    <li class="list-group-item border-0 bg-transparent"><input type="checkbox" class="filter-state" value="Terengganu"> Terengganu</li>
                </ul>


                <h6 class="mt-3">Pet Category</h6>
                <ul class="list-group border-0">
                    <li class="list-group-item border-0 bg-transparent"><input type="checkbox" class="filter-category" value="Cat"> Cat</li>
                    <li class="list-group-item border-0 bg-transparent"><input type="checkbox" class="filter-category" value="Dog"> Dog</li>
                </ul>

                <h6 class="mt-3">Gender</h6>
                <ul class="list-group border-0">
                    <li class="list-group-item border-0 bg-transparent"><input type="checkbox" class="filter-gender" value="Male"> Male</li>
                    <li class="list-group-item border-0 bg-transparent"><input type="checkbox" class="filter-gender" value="Female"> Female</li>
                </ul>

                <h6 class="mt-3">Age</h6>
                <ul class="list-group border-0">
                    <li class="list-group-item border-0 bg-transparent"><input type="checkbox" class="filter-age" value="baby"> Baby (< 1 year)</li>
                    <li class="list-group-item border-0 bg-transparent"><input type="checkbox" class="filter-age" value="young"> Young (1-3 years)</li>
                    <li class="list-group-item border-0 bg-transparent"><input type="checkbox" class="filter-age" value="adult"> Adult (4-8 years)</li>
                    <li class="list-group-item border-0 bg-transparent"><input type="checkbox" class="filter-age" value="senior"> Geriatric (9+ years)</li>
                </ul>
            </div>
        </div>

        <!-- Improved Pet Listing -->
        <div class="col-md-9">
            <div id="results-count" class="mb-3">
                Showing <span id="count-number">{{ count($pets) }}</span> pets
            </div>

            <div class="row" id="pet-list">
                @if(count($pets) > 0)
                @foreach($pets as $pet)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 shadow-sm transition-card">
                        <div class="position-relative">
                            @php
                            $imageFile = $pet->images->first()->ImagePath ?? 'default-pet.png';
                            $imagePath = str_contains($imageFile, 'images/') ? asset($imageFile) : asset('images/' . $imageFile);
                            @endphp

                            <img src="{{ $imagePath }}" class="card-img-top" alt="{{ $pet->PetName }}" 
                                 style="width: 100%; height: 250px; object-fit: cover;">
                            <span class="position-absolute top-0 end-0 badge badge-species m-2">{{ $pet->Species }}</span>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="card-title mb-0">{{ $pet->PetName }}</h5>
                            </div>
                            <p class="card-text">
                                <span class="badge bg-light text-dark me-1">{{ $pet->Breed }}</span>
                                <span class="badge bg-light text-dark me-1">{{ \Carbon\Carbon::parse($pet->DateOfBirth)->age }} years</span>
                                <span class="badge bg-light text-dark">{{ $pet->Color }}</span>
                            </p>
                            <p class="small text-muted">{{ Str::limit($pet->Personality, 60) }}</p>
                            <a href="{{ route('adopter.pet.details', ['id' => $pet->PetID]) }}" 
                               class="btn btn-view-details w-100">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
                @else
                <div class="col-12 text-center mt-4">
                    <h5>No pets available for adoption at the moment.</h5>
                </div>
                @endif
            </div>

            <div id="loading-indicator" class="text-center d-none my-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CSS Styles -->
<style>
    .btn-view-details {
        background-color: #1F4EA4 !important;
        border-color: #1F4EA4 !important;
        color: #fff !important;
    }

    .badge-species {
        background-color: #1F4EA4 !important;
        border-color: #1F4EA4 !important;
        color: #fff !important;
    }

    /* Card hover effects */
    .transition-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .transition-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }

    /* Custom checkbox styling */
    .list-group-item input[type="checkbox"] {
        margin-right: 10px;
    }

    /* Badge styles */
    .badge {
        font-weight: 500;
        padding: 0.5em 0.8em;
    }

    /* Filter section on mobile */
    @media (max-width: 767.98px) {
        .filter-section {
            margin-bottom: 20px;
        }
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const petList = document.getElementById("pet-list");
        const loadingIndicator = document.getElementById("loading-indicator");
        const countNumber = document.getElementById("count-number");

        // Get CSRF token for AJAX requests
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function fetchPets() {
            // Show loading indicator
            loadingIndicator.classList.remove('d-none');

            let searchQuery = document.getElementById('search-input').value.trim();
            let selectedStates = Array.from(document.querySelectorAll('.filter-state:checked')).map(el => el.value);
            let selectedCategories = Array.from(document.querySelectorAll('.filter-category:checked')).map(el => el.value);
            let selectedGenders = Array.from(document.querySelectorAll('.filter-gender:checked')).map(el => el.value);
            let selectedAges = Array.from(document.querySelectorAll('.filter-age:checked')).map(el => el.value);
            let sortOption = document.getElementById('sort-select').value;

            let url = new URL("{{ route('adopter.petlist') }}", window.location.origin);
            url.searchParams.append("search", searchQuery);
            url.searchParams.append("sort", sortOption);
            
            selectedStates.forEach(state => url.searchParams.append("state[]", state));
            selectedCategories.forEach(category => url.searchParams.append("category[]", category));
            selectedGenders.forEach(gender => url.searchParams.append("gender[]", gender));
            selectedAges.forEach(age => url.searchParams.append("age[]", age));

            fetch(url, {
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'text/html'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(html => {
                let parser = new DOMParser();
                let doc = parser.parseFromString(html, "text/html");
                let newPetList = doc.querySelector("#pet-list");

                if (newPetList) {
                    petList.innerHTML = newPetList.innerHTML;
                    // Update count
                    let petCount = newPetList.querySelectorAll(".card").length;
                    countNumber.textContent = petCount;
                } else {
                    petList.innerHTML = '<div class="col-12 text-center mt-4"><h5>No pets match your search criteria.</h5></div>';
                    countNumber.textContent = 0;
                }

                loadingIndicator.classList.add('d-none');
            })
            .catch(error => {
                console.error("Error fetching pets:", error);
                loadingIndicator.classList.add('d-none');
                petList.innerHTML = '<div class="col-12 text-center mt-4"><h5>An error occurred while fetching pets. Please try again.</h5></div>';
                showToast('Error loading pets. Please try again.', 'danger');
            });
        }

        // Initialize event listeners
        document.getElementById('search-input').addEventListener('input', debounce(fetchPets, 500));
        document.getElementById('sort-select').addEventListener('change', fetchPets);
        document.querySelectorAll('.filter-state, .filter-category, .filter-gender, .filter-age').forEach(filter => {
            filter.addEventListener('change', fetchPets);
        });

        // Clear filters button
        document.getElementById('clear-filters').addEventListener('click', function () {
            document.querySelectorAll('.filter-state, .filter-category, .filter-gender, .filter-age').forEach(checkbox => {
                checkbox.checked = false;
            });
            document.getElementById('search-input').value = '';
            document.getElementById('sort-select').selectedIndex = 0;
            fetchPets();
        });

        // Debounce function to prevent excessive API calls
        function debounce(func, wait) {
            let timeout;
            return function () {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, arguments), wait);
            };
        }

        // Toast notification function
        function showToast(message, type = 'info') {
            // Check if a toast container exists, create if not
            let toastContainer = document.getElementById('toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.id = 'toast-container';
                toastContainer.className = 'position-fixed bottom-0 end-0 p-3';
                document.body.appendChild(toastContainer);
            }

            // Create a unique ID for this toast
            const toastId = 'toast-' + Date.now();

            // Create toast HTML
            const toast = `
            <div id="${toastId}" class="toast align-items-center text-white bg-${type === 'success' ? 'success' : type === 'danger' ? 'danger' : 'primary'}" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
            `;

            // Add toast to container
            toastContainer.innerHTML += toast;

            // Initialize and show the toast
            const toastElement = document.getElementById(toastId);
            const bsToast = new bootstrap.Toast(toastElement, {delay: 3000});
            bsToast.show();

            // Remove toast after it's hidden
            toastElement.addEventListener('hidden.bs.toast', function () {
                toastElement.remove();
            });
        }
    });
</script>
@endsection