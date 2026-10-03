@extends('layouts.adopter_master')
@section('title', 'Privacy Policy - Petopia')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    /* Main container styles */
    .privacy-container {
        max-width: 1000px;
        margin: 0 auto;
    }
    
    /* Header styles */
    .privacy-header {
        background-color: #343a40;
        padding: 2rem 0;
        margin-bottom: 2rem; /* Ensure space between header and card */
    }
    
    .privacy-header h1 {
        font-size: 2.5rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    
    .privacy-header p {
        opacity: 0.8;
    }
    
    /* Card styles with proper spacing */
    .policy-card {
        border-radius: 12px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        margin-bottom: 3rem; /* Add space at bottom */
        overflow: hidden;
        border: none;
    }
    
    /* Fix z-index and positioning to prevent overlap */
    .main-content {
        position: relative;
        z-index: 10;
        padding-top: 1rem;
    }
    
    /* Updated timestamp styling */
    .updated-timestamp {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1rem;
        background-color: rgba(0, 123, 255, 0.1);
        color: #007bff;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 500;
        margin-bottom: 1.5rem;
    }
    
    .updated-timestamp i {
        margin-right: 0.5rem;
    }
    
    /* Section styling */
    .policy-section {
        margin-bottom: 2rem;
        padding: 0 1.5rem;
    }
    
    .policy-section:last-child {
        margin-bottom: 1rem;
    }
    
    /* Headers with icons */
    .section-header {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
        color: #007bff;
    }
    
    .section-header i {
        width: 32px;
        height: 32px;
        background-color: rgba(0, 123, 255, 0.1);
        color: #007bff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 0.8rem;
    }
    
    /* List styling with paw icon */
    .paw-list {
        list-style: none;
        padding-left: 0.5rem;
        margin-bottom: 1.5rem;
    }
    
    .paw-list li {
        position: relative;
        padding-left: 1.8rem;
        margin-bottom: 0.8rem;
        line-height: 1.6;
    }
    
    .paw-list li::before {
        content: "🐾";
        position: absolute;
        left: 0;
        color: #6c757d;
        font-size: 0.9rem;
    }
    
    /* Contact section */
    .contact-card {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 1.5rem;
        margin-top: 1rem;
        border-left: 4px solid #007bff;
    }
    
    .contact-info {
        display: flex;
        align-items: center;
    }
    
    .contact-info i {
        font-size: 1.2rem;
        margin-right: 1rem;
        color: #007bff;
    }
    
    .contact-info a {
        color: #007bff;
        font-weight: 500;
    }
    
    /* Card header and body */
    .card-header {
        background-color: #f8f9fa;
        padding: 1.5rem;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }
    
    .card-body {
        padding: 1.5rem 0; /* Horizontal padding is handled by sections */
    }
</style>
@endpush

@section('content')
<!-- Privacy Policy Header - Fixed position -->
<div class="privacy-header text-white">
    <div class="container text-center privacy-container">
        <h1>Privacy Policy</h1>
        <p>How we protect your information at Petopia</p>
    </div>
</div>

<!-- Main Content with proper spacing -->
<div class="container privacy-container main-content">
    <!-- Policy Card with proper margin to avoid overlap -->
    <div class="card policy-card">
        <!-- Card Header -->
        <div class="card-header">
            <span class="updated-timestamp">
                <i class="far fa-calendar-alt"></i>Last updated: March 2025
            </span>
            <p class="mb-0">At Petopia, we care about your privacy as much as we care about pets. Here's how we handle your information.</p>
        </div>
        
        <!-- Card Body -->
        <div class="card-body">
            <!-- Introduction Section -->
            <div class="policy-section">
                <h3 class="section-header">
                    <i class="fas fa-info-circle"></i>
                    <span>Introduction</span>
                </h3>
                <p>Your privacy is important to us. This privacy policy explains how we collect, use, and protect your personal information when you use Petopia. We've designed this policy to be clear, fair, and transparent about our data practices.</p>
            </div>
            
            <!-- Information We Collect Section -->
            <div class="policy-section">
                <h3 class="section-header">
                    <i class="fas fa-database"></i>
                    <span>Information We Collect</span>
                </h3>
                <p>To provide you with the best pet adoption experience, we collect certain information about you:</p>
                <ul class="paw-list">
                    <li>Personal details (name, email, phone number) when you register an account</li>
                    <li>Pet adoption application details to match you with your perfect pet</li>
                    <li>Browsing activity on our website to improve your experience</li>
                    <li>Communications between you and pet shelters or our support team</li>
                </ul>
            </div>
            
            <!-- How We Use Your Information Section -->
            <div class="policy-section">
                <h3 class="section-header">
                    <i class="fas fa-cogs"></i>
                    <span>How We Use Your Information</span>
                </h3>
                <ul class="paw-list">
                    <li>To process and manage your adoption applications</li>
                    <li>To facilitate communication between adopters and pet shelters</li>
                    <li>To improve our services and customize your experience</li>
                    <li>To send you updates about pets that match your preferences</li>
                    <li>To ensure the safety and security of our pet adoption platform</li>
                </ul>
            </div>
            
            <!-- Contact Us Section -->
            <div class="policy-section">
                <h3 class="section-header">
                    <i class="fas fa-envelope"></i>
                    <span>Contact Us</span>
                </h3>
                <p>If you have any questions about this policy or how we handle your information, please don't hesitate to reach out:</p>
                <div class="contact-card">
                    <div class="contact-info">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <strong>Email:</strong>
                            <a href="mailto:support@petopia.com">support@petopia.com</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection