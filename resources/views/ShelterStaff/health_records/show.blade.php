@extends('layouts.shelter_master')
@section('title', 'View Health Records')
@section('content')
<div class="container-fluid mt-4">
    <!-- Pet Profile Header -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-2 text-center">
                    @if($pet->images->isNotEmpty())
                    <img src="{{ asset($pet->images->first()->ImagePath) }}" alt="{{ $pet->PetName }}" 
                         class="rounded-circle img-thumbnail" style="width: 120px; height: 120px; object-fit: cover;">
                    @else
                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto" 
                         style="width: 120px; height: 120px;">
                        <i class="fas fa-{{ $pet->Species == 'Dog' ? 'dog' : ($pet->Species == 'Cat' ? 'cat' : 'paw') }} fa-3x text-secondary"></i>
                    </div>
                    @endif
                </div>
                <div class="col-md-7">
                    <h2 class="mb-1">{{ $pet->PetName }}</h2>
                    <div class="text-muted mb-2">
                        <span class="me-3"><i class="fas fa-tag me-1"></i> ID: P00{{ $pet->PetID }}</span>
                        <span class="me-3"><i class="fas fa-paw me-1"></i> {{ $pet->Species }}</span>
                        <span class="me-3"><i class="fas fa-venus-mars me-1"></i> {{ $pet->Gender ?? 'Unknown' }}</span>
                        <span><i class="fas fa-birthday-cake me-1"></i> {{ $pet->DateOfBirth ? \Carbon\Carbon::parse($pet->DateOfBirth)->format('d M Y') : 'Unknown' }}</span>
                    </div>
                    <div>
                        @if(!empty($pet->AllergyDetails))
                        <div class="alert alert-warning py-1 px-2 d-inline-block mb-0">
                            <i class="fas fa-exclamation-triangle me-1"></i> Allergic to: {{ $pet->AllergyDetails }}
                        </div>
                        @endif
                    </div>
                </div>
                <div class="col-md-3 text-end">
                    <a href="{{ route('shelter.health') }}" class="btn btn-outline-secondary mb-2">
                        <i class="fas fa-arrow-left me-1"></i> Back to Records
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Health Records Section -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
            <h3 class="mb-0"><i class="fas fa-heartbeat me-2"></i>Health Records</h3>
            <a href="{{ route('shelter.health.create', ['pet_id' => $pet->PetID]) }}" class="btn btn-light">
                <i class="fas fa-plus-circle me-1"></i> Add New Record
            </a>
        </div>
        <div class="card-body">
            @if($healthRecords->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-clipboard-list fa-4x text-muted mb-3"></i>
                <h4>No health records found</h4>
                <p class="text-muted">This pet doesn't have any health records yet.</p>
                <a href="{{ route('shelter.health.create', ['pet_id' => $pet->PetID]) }}" class="btn btn-primary mt-2">
                    <i class="fas fa-plus-circle me-1"></i> Add First Record
                </a>
            </div>
            @else
            <!-- Health Summary Cards -->
            <div class="row mb-4">
                @php
                $latestRecord = $healthRecords->sortByDesc('LastCheckupDate')->first();
                
                // Set health condition class based on condition
                $healthConditionClass = 'bg-success';
                if(isset($latestRecord->HealthCondition)) {
                    if($latestRecord->HealthCondition == 'Minor Issues') {
                        $healthConditionClass = 'bg-info';
                    } elseif($latestRecord->HealthCondition == 'Needs Attention') {
                        $healthConditionClass = 'bg-warning';
                    } elseif($latestRecord->HealthCondition == 'Critical') {
                        $healthConditionClass = 'bg-danger';
                    }
                }
                @endphp
                
                <!-- Latest Checkup -->
                <div class="col-md-3 mb-3">
                    <div class="card h-100 border-0 bg-light">
                        <div class="card-body">
                            <h5 class="card-title text-muted">
                                <i class="fas fa-calendar-check me-2"></i> Latest Checkup
                            </h5>
                            <h3 class="mb-1">{{ \Carbon\Carbon::parse($latestRecord->LastCheckupDate)->format('d M Y') }}</h3>
                            <p class="text-muted">{{ \Carbon\Carbon::parse($latestRecord->LastCheckupDate)->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Health Condition -->
                <div class="col-md-3 mb-3">
                    <div class="card h-100 border-0 bg-light">
                        <div class="card-body">
                            <h5 class="card-title text-muted">
                                <i class="fas fa-heartbeat me-2"></i> Health Condition
                            </h5>
                            <h3 class="mb-1">
                                <span class="badge {{ $healthConditionClass }} p-2">
                                    {{ $latestRecord->HealthCondition ?? 'Healthy' }}
                                </span>
                            </h3>
                            <p class="text-muted">Last updated: {{ \Carbon\Carbon::parse($latestRecord->LastCheckupDate)->format('d M Y') }}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Vaccination Status -->
                <div class="col-md-3 mb-3">
                    <div class="card h-100 border-0 bg-light">
                        <div class="card-body">
                            <h5 class="card-title text-muted">
                                <i class="fas fa-syringe me-2"></i> Vaccination Status
                            </h5>
                            <h3 class="mb-1">
                                <span class="badge 
                                      {{ $latestRecord->VaccinationStatus == 'Fully Vaccinated' ? 'bg-success' : 
                                           ($latestRecord->VaccinationStatus == 'Partially Vaccinated' ? 'bg-warning' : 'bg-danger') }}
                                      p-2">
                                    {{ $latestRecord->VaccinationStatus }}
                                </span>
                            </h3>
                            <p class="text-muted">Last updated: {{ \Carbon\Carbon::parse($latestRecord->LastCheckupDate)->format('d M Y') }}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Sterilization Status -->
                <div class="col-md-3 mb-3">
                    <div class="card h-100 border-0 bg-light">
                        <div class="card-body">
                            <h5 class="card-title text-muted">
                                <i class="fas fa-check-circle me-2"></i> Sterilization
                            </h5>
                            <h3 class="mb-1">
                                @if($latestRecord->Sterilization === 'Yes')
                                <span class="badge bg-success p-2">Neutered</span>
                                @else
                                <span class="badge bg-secondary p-2">Not Neutered</span>
                                @endif
                            </h3>
                            <p class="text-muted">
                                Last updated: 
                                {{ \Carbon\Carbon::parse($latestRecord->LastCheckupDate)->format('d M Y') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        
            <!-- Health Records Timeline -->
            <div class="mb-4">
                <h4 class="mb-3"><i class="fas fa-history me-2"></i>Health History</h4>
                
                <div class="timeline">
                    @foreach($healthRecords->sortByDesc('LastCheckupDate') as $record)
                    @php
                    // Set health condition class based on condition
                    $recordHealthClass = 'success';
                    if(isset($record->HealthCondition)) {
                        if($record->HealthCondition == 'Minor Issues') {
                            $recordHealthClass = 'info';
                        } elseif($record->HealthCondition == 'Needs Attention') {
                            $recordHealthClass = 'warning';
                        } elseif($record->HealthCondition == 'Critical') {
                            $recordHealthClass = 'danger';
                        }
                    }
                    @endphp
                    <div class="timeline-item border-start border-{{ $recordHealthClass }} ps-3 mb-4">
                        <div class="timeline-date bg-{{ $recordHealthClass }} text-white rounded p-2 d-inline-block mb-2">
                            <i class="fas fa-calendar me-1"></i> {{ \Carbon\Carbon::parse($record->LastCheckupDate)->format('d M Y') }}
                        </div>
                        
                        <div class="card shadow-sm">
                            <div class="card-header bg-light">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge bg-{{ $recordHealthClass }} me-2">
                                            <i class="fas fa-heartbeat me-1"></i> {{ $record->HealthCondition ?? 'Healthy' }}
                                        </span>
                                        
                                        <span class="badge 
                                            {{ $record->VaccinationStatus == 'Fully Vaccinated' ? 'bg-success' : 
                                                ($record->VaccinationStatus == 'Partially Vaccinated' ? 'bg-warning' : 'bg-danger') }}">
                                            <i class="fas fa-syringe me-1"></i> {{ $record->VaccinationStatus }}
                                        </span>
                                    </div>
                                    <a href="{{ route('shelter.health.edit', $record->RecordID) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <h5 class="mb-1"><i class="fas fa-stethoscope me-1"></i> Diagnosis</h5>
                                            <p>{{ !empty($record->Diagnosis) && $record->Diagnosis != 'No Diagnosis' ? $record->Diagnosis : 'No diagnosis provided' }}</p>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <h5 class="mb-1"><i class="fas fa-pills me-1"></i> Treatment/Medicine</h5>
                                            <p>{{ !empty($record->Medicine) && $record->Medicine != 'No Medicine' ? $record->Medicine : 'No medicine prescribed' }}</p>
                                        </div>
                                        
                                        <div>
                                            <h5 class="mb-1"><i class="fas fa-clipboard me-1"></i> Remarks</h5>
                                            <p class="mb-0">{{ !empty($record->HealthRemarks) ? $record->HealthRemarks : 'No remarks provided' }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        @if($record->images->isNotEmpty())
                                        <h5 class="mb-2"><i class="fas fa-images me-1"></i> Images</h5>
                                        <div class="image-gallery">
                                            @foreach($record->images as $image)
                                            <a href="{{ asset('storage/' . $image->ImagePath) }}" 
                                               data-lightbox="record-{{ $record->RecordID }}" 
                                               data-title="Health Record: {{ \Carbon\Carbon::parse($record->LastCheckupDate)->format('d M Y') }}">
                                                <img src="{{ asset('storage/' . $image->ImagePath) }}" 
                                                     class="img-thumbnail gallery-thumbnail" alt="Health Record Image">
                                            </a>
                                            @endforeach
                                        </div>
                                        @else
                                        <div class="text-center pt-4">
                                            <i class="fas fa-image fa-3x text-muted mb-2"></i>
                                            <p class="text-muted">No images available</p>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        @endif
        </div>
    </div>
</div>

<!-- Custom Styles -->
<style>
    .gallery-thumbnail {
        width: 80px;
        height: 80px;
        object-fit: cover;
        margin-right: 5px;
        margin-bottom: 5px;
        transition: transform 0.2s;
    }

    .gallery-thumbnail:hover {
        transform: scale(1.1);
    }

    .image-gallery {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.05);
    }
    
    .timeline-item {
        position: relative;
    }
    
    .timeline-item::before {
        content: "";
        position: absolute;
        left: -8px;
        top: 0;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background-color: currentColor;
    }
    
    .card {
        border-radius: 8px;
        overflow: hidden;
    }
    
    .badge {
        border-radius: 4px;
        padding: 6px 10px;
    }
</style>

<!-- Include Lightbox for image gallery if not already included in your layout -->
@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
<script>
// Initialize lightbox
document.addEventListener('DOMContentLoaded', function () {
    lightbox.option({
        'resizeDuration': 200,
        'wrapAround': true,
        'albumLabel': "Image %1 of %2"
    });
});
</script>
@endpush
@endsection