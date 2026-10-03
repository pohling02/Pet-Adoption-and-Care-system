@extends('layouts.adopter_master')
@section('title', 'Contact Us - Petopia')
@section('content')
<div class="container my-5">
    <div class="row justify-content-center mb-4">
        <div class="col-lg-8 text-center">
            <h2 class="display-5 mb-3">Contact Us</h2>
            <p class="lead text-secondary">Have questions about adoption or pet care? We're here to help!</p>
            <hr class="my-4" style="width: 50%; margin: 0 auto;">
        </div>
    </div>

    <div class="row g-4">
        <!-- Contact Information Card -->
        <div class="col-md-5">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header py-3 text-white" style="background-color: #343a40;">
                    <h4 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i> Our Information</h4>
                </div>
                <div class="card-body">
                    <div class="d-flex mb-3">
                        <div class="me-3">
                            <i class="fas fa-home" style="color: #343a40; font-size: 1.5rem;"></i>
                        </div>
                        <div>
                            <h5 class="mb-1">Petopia Adoption Center</h5>
                            <p class="text-muted mb-0">Ground Floor, Block A, Setapak<br>Kuala Lumpur, Malaysia</p>
                        </div>
                    </div>
                    
                    <div class="d-flex mb-3">
                        <div class="me-3">
                            <i class="fas fa-envelope" style="color: #343a40; font-size: 1.5rem;"></i>
                        </div>
                        <div>
                            <h5 class="mb-1">Email</h5>
                            <a href="mailto:ngpt-wm22@student.tarc.edu.my" class="text-decoration-none">ngpt-wm22@student.tarc.edu.my</a>
                        </div>
                    </div>
                    
                    <div class="d-flex mb-3">
                        <div class="me-3">
                            <i class="fas fa-phone" style="color: #343a40; font-size: 1.5rem;"></i>
                        </div>
                        <div>
                            <h5 class="mb-1">Phone</h5>
                            <p class="text-muted mb-0">019 456 7890</p>
                        </div>
                    </div>
                    
                    <div class="d-flex">
                        <div class="me-3">
                            <i class="fas fa-clock" style="color: #343a40; font-size: 1.5rem;"></i>
                        </div>
                        <div>
                            <h5 class="mb-1">Operating Hours</h5>
                            <p class="text-muted mb-0">Monday - Friday: 9AM - 6PM<br>Saturday: 10AM - 4PM<br>Sunday: Closed</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Contact Form Card -->
        <div class="col-md-7">
            <div class="card shadow-sm border-0">
                <div class="card-header py-3 text-white" style="background-color: #343a40;">
                    <h4 class="mb-0"><i class="fas fa-paper-plane me-2"></i> Send Us a Message</h4>
                </div>
                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    <form action="{{ route('contact.send') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Your Name" required>
                                    <label for="name">Your Name</label>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="name@example.com" required>
                                    <label for="email">Your Email</label>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control @error('subject') is-invalid @enderror" id="subject" name="subject" placeholder="Subject" required>
                            <label for="subject">Subject</label>
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-floating mb-3">
                            <select class="form-select @error('inquiry_type') is-invalid @enderror" id="inquiry_type" name="inquiry_type">
                                <option value="" selected disabled>Please select</option>
                                <option value="adoption">Pet Adoption</option>
                                <option value="donation">Donations</option>
                                <option value="volunteer">Volunteering</option>
                                <option value="fostering">Pet Fostering</option>
                                <option value="other">Other</option>
                            </select>
                            <label for="inquiry_type">Inquiry Type</label>
                            @error('inquiry_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-floating mb-3">
                            <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" placeholder="Your message here" style="height: 150px" required></textarea>
                            <label for="message">Your Message</label>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="newsletter" name="newsletter">
                            <label class="form-check-label" for="newsletter">
                                Subscribe to our newsletter for adoption updates and pet care tips
                            </label>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn text-white py-2" style="background-color: #343a40;">
                                <i class="fas fa-paper-plane me-2"></i> Send Message
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Map Section - Kept but renamed to avoid overlap -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header py-3 text-white" style="background-color: #343a40;">
                    <h4 class="mb-0"><i class="fas fa-map-marked-alt me-2"></i> Our Location</h4>
                </div>
                <div class="card-body p-0">
                    <div class="ratio ratio-21x9">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15934.140788281122!2d101.73185714063922!3d3.215924693254899!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31cc3843bfb6a031%3A0x2dc5e067aae3ab84!2sTunku%20Abdul%20Rahman%20University%20of%20Management%20and%20Technology%20(TAR%20UMT)!5e0!3m2!1sen!2smy!4v1742021052915!5m2!1sen!2smy" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                            style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection