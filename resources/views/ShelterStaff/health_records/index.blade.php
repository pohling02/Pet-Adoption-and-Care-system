@extends('layouts.shelter_master')

@section('title', 'Pet Health Records')

@section('content')
<div class="container-fluid mt-4">
    <!-- Summary Cards Row -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm bg-success text-white">
                <div class="card-body text-center">
                    <h5 class="card-title"><i class="fas fa-check-circle me-2"></i>Fully Vaccinated</h5>
                    <h2>{{ $latestRecords->where('VaccinationStatus', 'Fully Vaccinated')->count() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm bg-warning text-white">
                <div class="card-body text-center">
                    <h5 class="card-title"><i class="fas fa-exclamation-triangle me-2"></i>Partially Vaccinated</h5>
                    <h2>{{ $latestRecords->where('VaccinationStatus', 'Partially Vaccinated')->count() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm bg-danger text-white">
                <div class="card-body text-center">
                    <h5 class="card-title"><i class="fas fa-times-circle me-2"></i>Not Vaccinated</h5>
                    <h2>{{ $latestRecords->where('VaccinationStatus', 'Not Vaccinated')->count() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm bg-primary text-white">
                <div class="card-body text-center">
                    <h5 class="card-title"><i class="fas fa-paw me-2"></i>Total Pets</h5>
                    <h2>{{ $healthRecords->groupBy('PetID')->count() }}</h2>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Card -->
    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h3 class="mb-0"><i class="fas fa-heartbeat me-2"></i>Pet Health Records</h3>
            <a href="{{ route('shelter.health.create') }}" class="btn btn-light">
                <i class="fas fa-plus-circle me-1"></i> Add New Record
            </a>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <!-- Search Bar -->
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-primary text-white"><i class="fas fa-search"></i></span>
                        <input type="text" id="searchInput" class="form-control" placeholder="Search by Pet Name, Species or Health Condition...">
                    </div>
                </div>
                <!-- Filters -->
                <div class="col-md-6">
                    <div class="d-flex gap-2 justify-content-end">
                        <select id="vaccineFilter" class="form-select">
                            <option value="">All Vaccination Status</option>
                            <option value="Fully Vaccinated">Fully Vaccinated</option>
                            <option value="Partially Vaccinated">Partially Vaccinated</option>
                            <option value="Not Vaccinated">Not Vaccinated</option>
                        </select>
                        <select id="healthFilter" class="form-select">
                            <option value="">All Health Conditions</option>
                            <option value="Healthy">Healthy</option>
                            <option value="Minor Issues">Minor Issues</option>
                            <option value="Needs Attention">Needs Attention</option>
                            <option value="Critical">Critical</option>
                        </select>
                        <select id="sortSelect" class="form-select">
                            <option value="newest">Newest First</option>
                            <option value="oldest">Oldest First</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle" id="healthRecordsTable">
                    <thead class="table-dark">
                        <tr>
                            <th>Pet ID</th>
                            <th>Pet Info</th>
                            <th>Vaccination</th>
                            <th>Health Condition</th>
                            <th>Last Check-up</th>
                            <th>Diagnosis & Remarks</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $currentPetId = null; @endphp
                        @foreach($healthRecords->groupBy('PetID') as $petID => $records)
                        @php 
                        $latestRecord = $records->sortByDesc('LastCheckupDate')->first();
                        $petInfo = $latestRecord->pet;
                        
                        // Set health condition class based on condition
                        $healthConditionClass = 'bg-success';
                        if($latestRecord->HealthCondition == 'Minor Issues') {
                            $healthConditionClass = 'bg-info';
                        } elseif($latestRecord->HealthCondition == 'Needs Attention') {
                            $healthConditionClass = 'bg-warning';
                        } elseif($latestRecord->HealthCondition == 'Critical') {
                            $healthConditionClass = 'bg-danger';
                        }
                        @endphp

                        <tr class="health-record-row @if($loop->odd) bg-light @endif" 
                            data-pet-name="{{ $petInfo->PetName }}" 
                            data-species="{{ $petInfo->Species }}" 
                            data-vaccination="{{ $latestRecord->VaccinationStatus }}"
                            data-health-condition="{{ $latestRecord->HealthCondition ?? 'Healthy' }}"
                            data-date="{{ \Carbon\Carbon::parse($latestRecord->LastCheckupDate)->format('Y-m-d') }}">
                            <td>
                                <span class="badge bg-secondary">P00{{ $petInfo->PetID }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($petInfo->images->isNotEmpty() && $petInfo->images->first()->ImagePath)
                                    <img src="{{ asset($petInfo->images->first()->ImagePath) }}" alt="{{ $petInfo->PetName }}" 
                                         class="rounded-circle me-2" width="50" height="50" style="object-fit: cover;">
                                    @else
                                    <div class="rounded-circle me-2 bg-secondary d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <i class="fas {{ $petInfo->Species == 'Dog' ? 'fa-dog' : ($petInfo->Species == 'Cat' ? 'fa-cat' : 'fa-paw') }} text-white"></i>
                                    </div>
                                    @endif
                                    <div>
                                        <h5 class="mb-0">{{ $petInfo->PetName }}</h5>
                                        <small class="text-muted">{{ $petInfo->Species }}, {{ $petInfo->Breed }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge 
                                      {{ $latestRecord->VaccinationStatus == 'Fully Vaccinated' ? 'bg-success' : 
                                        ($latestRecord->VaccinationStatus == 'Partially Vaccinated' ? 'bg-warning' : 'bg-danger') }}
                                      py-2 px-3">
                                    <i class="fas {{ $latestRecord->VaccinationStatus == 'Fully Vaccinated' ? 'fa-check-circle' : 
                                        ($latestRecord->VaccinationStatus == 'Partially Vaccinated' ? 'fa-exclamation-circle' : 'fa-times-circle') }} me-1"></i>
                                    {{ $latestRecord->VaccinationStatus }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $healthConditionClass }} py-2 px-3">
                                    <i class="fas fa-heartbeat me-1"></i>
                                    {{ $latestRecord->HealthCondition ?? 'Healthy' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span>{{ \Carbon\Carbon::parse($latestRecord->LastCheckupDate)->format('d M Y') }}</span>
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($latestRecord->LastCheckupDate)->diffForHumans() }}
                                    </small>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div>
                                        @if($latestRecord->Diagnosis)
                                        <span class="fw-bold">{{ $latestRecord->Diagnosis }}</span><br>
                                        @endif
                                        <small class="text-muted">{{ $latestRecord->HealthRemarks ?: 'No remarks' }}</small>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('shelter.health.view', ['id' => $petInfo->PetID]) }}" 
                                       class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="View Health Records">
                                        <i class="fas fa-file-medical"></i>
                                    </a>
                                    <a href="{{ route('shelter.health.edit', $latestRecord->RecordID) }}" 
                                       class="btn btn-sm btn-outline-warning" data-bs-toggle="tooltip" title="Edit Record">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- For empty state -->
            @if(count($healthRecords) == 0)
            <div class="text-center py-5">
                <div class="mb-3">
                    <i class="fas fa-notes-medical fa-4x text-muted"></i>
                </div>
                <h4>No health records found</h4>
                <p class="text-muted">Start by adding a new health record</p>
                <a href="{{ route('shelter.health.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle me-1"></i> Add New Record
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Styles -->
<style>
    .status-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.05);
    }

    .health-record-row {
        transition: all 0.2s;
    }

    th {
        white-space: nowrap;
    }
    
    .card {
        transition: transform 0.3s;
    }
    
    .card:hover {
        transform: translateY(-5px);
    }
    
    .btn-group .btn {
        border-radius: 4px;
        margin: 0 2px;
    }
</style>

<!-- Scripts -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const searchInput = document.getElementById("searchInput");
        const vaccineFilter = document.getElementById("vaccineFilter");
        const healthFilter = document.getElementById("healthFilter");
        const sortSelect = document.getElementById("sortSelect");
        const tableRows = document.querySelectorAll(".health-record-row");
        const tableBody = document.querySelector("#healthRecordsTable tbody");
        
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Filter function that combines search and dropdown filters
        function filterTable() {
            const searchQuery = searchInput.value.toLowerCase();
            const vaccineQuery = vaccineFilter.value;
            const healthQuery = healthFilter.value;

            tableRows.forEach(row => {
                const petName = row.getAttribute("data-pet-name").toLowerCase();
                const species = row.getAttribute("data-species").toLowerCase();
                const vaccination = row.getAttribute("data-vaccination");
                const healthCondition = row.getAttribute("data-health-condition");
                
                // Get diagnosis text from the row
                const diagnosisText = row.querySelector("td:nth-child(6)").textContent.toLowerCase();

                const matchesSearch = petName.includes(searchQuery) || 
                                     species.includes(searchQuery) || 
                                     diagnosisText.includes(searchQuery);
                                     
                const matchesVaccine = !vaccineQuery || vaccination === vaccineQuery;
                const matchesHealth = !healthQuery || healthCondition === healthQuery;

                row.style.display = (matchesSearch && matchesVaccine && matchesHealth) ? "" : "none";
            });
        }

        // Sort function
        function sortTable() {
            const rows = Array.from(tableRows);
            const sortOrder = sortSelect.value === "oldest" ? 1 : -1;

            rows.sort((a, b) => {
                const dateA = new Date(a.getAttribute("data-date"));
                const dateB = new Date(b.getAttribute("data-date"));
                return (dateA - dateB) * sortOrder;
            });

            // Remove all existing rows
            rows.forEach(row => row.remove());

            // Re-append in sorted order
            rows.forEach(row => tableBody.appendChild(row));
        }

        // Add event listeners
        searchInput.addEventListener("input", filterTable);
        vaccineFilter.addEventListener("change", filterTable);
        healthFilter.addEventListener("change", filterTable);
        sortSelect.addEventListener("change", sortTable);

        // Initialize sort on page load
        sortTable();
    });
</script>
@endsection