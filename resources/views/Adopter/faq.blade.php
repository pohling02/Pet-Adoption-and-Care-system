@extends('layouts.adopter_master')

@section('title', 'FAQ - Petopia')

@section('content')
<div class="container-fluid mt-5"> 
    <h2 class="text-center">Frequently Asked Questions</h2>
    <div class="container"> 
        <div class="accordion mt-4" id="faqAccordion">

            <!-- Question 1: Adoption Process -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                        How do I adopt a pet?
                    </button>
                </h2>
                <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        To adopt a pet, follow these steps:
                        <ol>
                            <li>Browse the available pets on our platform.</li>
                            <li>Select the pet that best matches your lifestyle and needs.</li>
                            <li>Click the "Apply for Adoption" button and fill out the adoption application form.</li>
                            <li>Wait for our adoption team to review your application.</li>
                            <li>If your application is shortlisted, we will arrange a video call or an in-person meeting.</li>
                            <li>Upon approval, you will complete the adoption agreement and pay any necessary fees.</li>
                            <li>After finalizing the process, you can bring your new pet home!</li>
                        </ol>
                        We aim to match pets with the most suitable adopters, ensuring their long-term well-being.
                    </div>
                </div>
            </div>

            <!-- Question 2: Adoption Fees -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                        What are the adoption fees?
                    </button>
                </h2>
                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        The adoption fees vary depending on the pet and its medical history. The fees typically cover:
                        <ul>
                            <li>Vaccinations (Rabies, Parvovirus, Distemper, etc.)</li>
                            <li>Deworming and flea/tick treatment</li>
                            <li>Spaying/neutering (if applicable)</li>
                            <li>Microchipping</li>
                            <li>Basic veterinary health check-ups</li>
                        </ul>
                        Some pets may have a lower or waived adoption fee if they are sponsored by donors or organizations.
                    </div>
                </div>
            </div>

            <!-- Question 3: Documents Needed -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                        What documents do I need to adopt a pet?
                    </button>
                </h2>
                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        To complete an adoption, you will need:
                        <ul>
                            <li>A copy of your **MyKad (IC) or Passport** for identification.</li>
                            <li>A recent **utility bill or tenancy agreement** to verify your address.</li>
                            <li>If you live in a rental property, you may need a **permission letter from your landlord**.</li>
                        </ul>
                        These documents help ensure a safe and responsible adoption process.
                    </div>
                </div>
            </div>

            <!-- Question 4: Pets in Apartments -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                        Can I adopt if I live in an apartment?
                    </button>
                </h2>
                <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Yes, you can adopt if you live in an apartment, but there are some important factors to consider:
                        <ul>
                            <li>Check your **building’s pet policy** – some apartments have restrictions on pet sizes or breeds.</li>
                            <li>Consider **your space** – larger pets may require more room to move around.</li>
                            <li>Pets need **regular outdoor activities**, so ensure you have access to parks or walking areas.</li>
                        </ul>
                        If your apartment allows pets, we recommend adopting **small to medium-sized breeds** that are better suited to indoor living.
                    </div>
                </div>
            </div>

            <!-- Question 5: Spaying/Neutering -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                        Do I need to spay/neuter my adopted pet?
                    </button>
                </h2>
                <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Yes, spaying/neutering is highly encouraged and is often a requirement before adoption. 
                        <br><br>
                        **Benefits of Spaying/Neutering:**
                        <ul>
                            <li>Reduces pet overpopulation.</li>
                            <li>Prevents certain health problems, such as uterine infections and testicular cancer.</li>
                            <li>Reduces aggressive behaviors and roaming tendencies.</li>
                        </ul>
                        If the pet has not been spayed/neutered before adoption, you may be required to sign an agreement to do so within a certain timeframe.
                    </div>
                </div>
            </div>

            <!-- Question 6: Meeting the Pet -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq6">
                        Can I meet the pet before adoption?
                    </button>
                </h2>
                <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Yes! We encourage adopters to meet their potential pets before finalizing the adoption. 
                        <br><br>
                        You can schedule a **virtual video call** or an **in-person meeting** at our shelter. 
                        <br><br>
                        This helps you interact with the pet, understand its behavior, and ensure a good match for your home and lifestyle.
                    </div>
                </div>
            </div>

            <!-- Question 7: Returning the Pet -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq7">
                        What if I can no longer take care of my adopted pet?
                    </button>
                </h2>
                <div id="faq7" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        If you can no longer care for your pet, please **contact our adoption team** immediately. 
                        <br><br>
                        We will assist you in finding a new home for your pet. Depending on the circumstances, you may:
                        <ul>
                            <li>Return the pet to our shelter (based on space availability).</li>
                            <li>Use our platform to find a new responsible adopter.</li>
                        </ul>
                        Abandoning a pet is not acceptable, and we encourage responsible pet ownership.
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
