@extends('layouts.adopter_master')
@section('title', 'Terms of Service - Petopia')
@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header py-3 text-white" style="background-color: #343a40;">
                    <h2 class="text-center mb-0">Terms of Service</h2>
                </div>
                <div class="card-body p-4">
                    <div class="alert alert-info">
                        <small><strong>Effective Date:</strong> March 2025</small>
                    </div>
                    
                    <div class="table-of-contents mb-4">
                        <h6 class="text-secondary">Contents:</h6>
                        <nav>
                            <ul class="list-unstyled">
                                <li><a href="#acceptance" class="text-decoration-none">1. Acceptance of Terms</a></li>
                                <li><a href="#responsibilities" class="text-decoration-none">2. User Responsibilities</a></li>
                                <li><a href="#adoption" class="text-decoration-none">3. Pet Adoption Process</a></li>
                                <li><a href="#prohibited" class="text-decoration-none">4. Prohibited Activities</a></li>
                                <li><a href="#liability" class="text-decoration-none">5. Limitation of Liability</a></li>
                                <li><a href="#termination" class="text-decoration-none">6. Termination</a></li>
                                <li><a href="#contact" class="text-decoration-none">7. Contact Us</a></li>
                            </ul>
                        </nav>
                    </div>
                    
                    <section id="acceptance" class="mb-4">
                        <h4 style="color: #343a40;">1. Acceptance of Terms</h4>
                        <p>By accessing or using the Petopia platform ("Service"), you acknowledge that you have read, understood, and agree to be bound by these Terms of Service. If you disagree with any part of these terms, you may not access the Service.</p>
                    </section>
                    
                    <section id="responsibilities" class="mb-4">
                        <h4 style="color: #343a40;">2. User Responsibilities</h4>
                        <div class="card bg-light">
                            <div class="card-body">
                                <ul class="mb-0">
                                    <li>You must provide accurate and complete information when registering an account or applying for pet adoption.</li>
                                    <li>You agree to treat all animals with care, compassion, and responsibility in accordance with animal welfare laws.</li>
                                    <li>You are responsible for maintaining the confidentiality of your account credentials and for all activities that occur under your account.</li>
                                    <li>You must promptly notify Petopia of any security breaches or unauthorized use of your account.</li>
                                </ul>
                            </div>
                        </div>
                    </section>
                    
                    <section id="adoption" class="mb-4">
                        <h4 style="color: #343a40;">3. Pet Adoption Process</h4>
                        <p>All adoption applications are subject to review and approval by our team. Petopia reserves the right to decline applications if:</p>
                        <ul>
                            <li>The adopter's living situation is deemed unsuitable for the specific pet.</li>
                            <li>The adopter's experience level does not match the pet's needs.</li>
                            <li>There are concerns about the potential welfare of the animal.</li>
                            <li>The application contains false or misleading information.</li>
                        </ul>
                        <p>Approved adopters may be subject to home visits and follow-up checks to ensure the wellbeing of adopted pets.</p>
                    </section>
                    
                    <section id="prohibited" class="mb-4">
                        <h4 style="color: #343a40;">4. Prohibited Activities</h4>
                        <p>Users are strictly prohibited from:</p>
                        <div class="row">
                            <div class="col-md-6">
                                <ul>
                                    <li>Posting false or misleading information about themselves or animals.</li>
                                    <li>Engaging in harassment, abuse, or discriminatory behavior.</li>
                                    <li>Using our platform for illegal activities including animal trafficking.</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul>
                                    <li>Attempting to circumvent our adoption screening process.</li>
                                    <li>Creating multiple accounts to bypass restrictions.</li>
                                    <li>Using our platform for commercial breeding purposes.</li>
                                </ul>
                            </div>
                        </div>
                    </section>
                    
                    <section id="liability" class="mb-4">
                        <h4 style="color: #343a40;">5. Limitation of Liability</h4>
                        <p>While Petopia strives to facilitate responsible pet adoptions, we cannot guarantee the health, temperament, or behavior of any animal. Petopia is not liable for:</p>
                        <ul>
                            <li>Any disputes arising between adopters and previous pet owners.</li>
                            <li>Medical conditions not disclosed or unknown at the time of adoption.</li>
                            <li>Behavioral issues that may develop after adoption.</li>
                            <li>Any damages or injuries caused by or to adopted pets.</li>
                        </ul>
                    </section>
                    
                    <section id="termination" class="mb-4">
                        <h4 style="color: #343a40;">6. Termination</h4>
                        <p>Petopia reserves the right to suspend or terminate user accounts without prior notice if:</p>
                        <ul>
                            <li>The user violates these Terms of Service.</li>
                            <li>The user engages in behavior that poses a risk to animal welfare.</li>
                            <li>The user's conduct could harm Petopia's reputation or operations.</li>
                        </ul>
                        <p>Users may appeal account termination by contacting our support team.</p>
                    </section>
                    
                    <section id="contact" class="mb-4">
                        <h4 style="color: #343a40;">7. Contact Us</h4>
                        <div class="card" style="border-color: #343a40;">
                            <div class="card-body">
                                <p class="mb-1">For questions or concerns regarding these Terms of Service, please contact us at:</p>
                                <div class="d-flex align-items-center mt-2">
                                    <i class="fas fa-envelope me-2" style="color: #343a40;"></i>
                                    <a href="mailto:support@petopia.com" class="text-decoration-none">support@petopia.com</a>
                                </div>
                                <div class="d-flex align-items-center mt-2">
                                    <i class="fas fa-phone me-2" style="color: #343a40;"></i>
                                    <span>(555) 123-4567</span>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
                <div class="card-footer bg-light text-center">
                    <small class="text-muted">Last updated: March 15, 2025</small>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
