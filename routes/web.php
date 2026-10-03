<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;
use App\Http\Controllers\Adopter\AdopterDashboardController;
use App\Http\Controllers\Adopter\AdoptionRequestController;
use App\Http\Controllers\Adopter\PetController;
use App\Http\Controllers\Adopter\AppointmentController as AdopterAppointmentController;
use App\Http\Controllers\Auth\AdopterAuthController;
use App\Http\Controllers\Auth\ShelterAuthController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Shelter\ShelterController;
use App\Http\Controllers\Shelter\PetManagementController;
use App\Http\Controllers\Shelter\AppointmentController as ShelterAppointmentController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Common\UserProfileController;
use App\Http\Controllers\Common\MessageController;
use App\Http\Controllers\Common\NotificationController;
use App\Http\Controllers\Common\PetResourceController;
use App\Http\Middleware\UnreadMessagesMiddleware;
use App\Models\User;

// ---------------------- WELCOME PAGE ----------------------
// Route::get('/', function () {
//     return view('welcome');
// });

// Redirect login route to Adopter login
Route::get('/login', function () {
    return redirect()->route('adopter.login');
})->name('login');

Route::get('/faq', [AdopterDashboardController::class, 'faq'])->name('faq');
Route::get('/contact', function () {
    return view('Adopter.contact');
})->name('contact');

Route::get('/privacy-policy', function () {
    return view('Adopter.privacy_policy');
})->name('privacy.policy');

Route::get('/terms-of-service', function () {
    return view('Adopter.terms_of_service.blade.php');
})->name('terms.service');

// ---------------------- ADMIN ROUTES ----------------------
Route::prefix('admin')->group(function () {
    // Admin Authentication Routes
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    // Protected Routes for Admin
    Route::middleware(['admin', 'check.restricted'])->group(function () {
        Route::get('/home', [UserManagementController::class, 'home'])->name('admin.home');
        Route::get('/manage-users', [UserManagementController::class, 'manageUsers'])->name('admin.manage_users');
        Route::delete('/delete-user/{userId}', [UserManagementController::class, 'deleteUser'])->name('admin.delete_user');
        Route::post('/reset-password/{id}', [UserManagementController::class, 'resetPassword'])->name('admin.reset_password');

        Route::get('/shelter-requests', [UserManagementController::class, 'shelterRequests'])->name('admin.shelter_requests');
        Route::post('/change-role/{userId}', [UserManagementController::class, 'changeUserRole'])->name('admin.change_role');

        Route::post('/approve-shelter/{userId}', [UserManagementController::class, 'approveShelter'])->name('admin.approve_shelter');
        Route::post('/reject-shelter/{userId}', [UserManagementController::class, 'rejectShelter'])->name('admin.reject_shelter');
        Route::post('/restrict-user/{userId}', [UserManagementController::class, 'restrictUser'])->name('admin.restrict_user');
        Route::post('/unrestrict-user/{userId}', [UserManagementController::class, 'unrestrictUser'])
            ->name('admin.unrestrict_user');
    });
});

// ---------------------- ADOPTER ROUTES ----------------------
Route::prefix('adopter')->group(function () {

    // Guest-only routes (Login, Register)
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AdopterAuthController::class, 'showLoginForm'])->name('adopter.login');
        Route::post('/login', [AdopterAuthController::class, 'login']);
        Route::get('/register', [AdopterAuthController::class, 'showRegisterForm'])->name('adopter.register.form');
        Route::post('/register', [AdopterAuthController::class, 'register'])->name('adopter.register');

        Route::get('/password/reset', [AdopterAuthController::class, 'showForgotPasswordForm'])->name('adopter.password.request');
        Route::post('/password/email', [AdopterAuthController::class, 'sendResetLinkEmail'])->name('adopter.password.email');
        Route::get('/password/reset/{token}', [AdopterAuthController::class, 'showResetPasswordForm'])->name('adopter.password.reset');
        Route::post('/password/reset', [AdopterAuthController::class, 'resetPassword'])->name('adopter.password.update');
    });

    // Authenticated-only routes
    Route::middleware(['auth', 'check.restricted'])->group(function () {
        Route::get('/adopter/home', [AdopterDashboardController::class, 'index'])->name('adopter.home');
        Route::get('/adopter/recommended-pets', [PetController::class, 'recommendPets'])->name('adopter.recommendedPets');
        Route::post('/logout', [AdopterAuthController::class, 'logout'])->name('adopter.logout');
        Route::get('/adopter/change-password', [AdopterAuthController::class, 'showChangePasswordForm'])
            ->name('adopter.password.form');
        Route::post('/adopter/update-pw', [AdopterAuthController::class, 'updatePassword'])
            ->name('adopter.pw.update');
        Route::get('/adopt-pet', [AdopterDashboardController::class, 'showAdoptionPage'])->name('adopter.petlist');
        Route::get('/pet/{id}', [PetController::class, 'show'])->name('adopter.pet.details');
        Route::get('/adopter/pets/pet_profile', [PetController::class, 'petProfile'])->name('adopter.pets.profile');
        Route::get('/adopter/pet/{id}/details', [PetController::class, 'petDetails'])->name('pet.details');

        Route::get('/adopter/adoptions', [AdoptionRequestController::class, 'index'])->name('adopter.adoption');
        Route::get('/adoption-form/{pet}', [AdoptionRequestController::class, 'create'])->name('adoption.create');
        Route::post('/adoption-request/{pet}', [AdoptionRequestController::class, 'store'])->name('adoption.request');
        Route::get('/adoption/{id}/details', [AdoptionRequestController::class, 'show'])->name('adoption.details');
        Route::get('/adoption/{id}/resubmit', [AdoptionRequestController::class, 'resubmitForm'])->name('adoption.resubmit');
        Route::post('/adoption/{id}/resubmit', [AdoptionRequestController::class, 'resubmit'])->name('adoption.resubmit.submit');
        Route::post('/adopter/adoption/{id}/cancel', [AdoptionRequestController::class, 'cancel'])->name('adoption.cancel');

        Route::get('/adopter/appointments', [AdopterAppointmentController::class, 'index'])->name('adopter.appointments');
        Route::get('/appointments/create', [AdopterAppointmentController::class, 'create'])->name('appointments.create');
        Route::get('/appointments/{id}', [AdopterAppointmentController::class, 'show'])->name('appointment.details');
        Route::post('/appointments/store', [AdopterAppointmentController::class, 'store'])->name('appointments.store');
        Route::get('/adopter/appointments/success/{appointment}', function ($appointmentID) {
            $appointment = \App\Models\Appointment::with(['pet', 'doctor'])->findOrFail($appointmentID);
            return view('adopter.appointment.success', compact('appointment'));
        })->name('adopter.appointment.success');
        Route::get('/appointments/{appointment}/reschedule', [AdopterAppointmentController::class, 'edit'])->name('appointments.reschedule.form');
        Route::put('/appointments/{appointment}/reschedule', [AdopterAppointmentController::class, 'reschedule'])->name('appointments.reschedule');
        Route::get('/adopter/appointments/get-timeslots', [AdopterAppointmentController::class, 'getTimeSlots'])->name('appointments.get-timeslots');
        Route::post('/appointments/{appointment}/cancel', [AdopterAppointmentController::class, 'cancel'])->name('appointments.cancel');

        Route::prefix('/notifications')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('adopter.notifications');
            Route::post('/mark-read/{id}', [NotificationController::class, 'markRead'])->name('notifications.markRead');
            Route::get('/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
            Route::delete('/notifications/delete/{id}', [NotificationController::class, 'deleteNotification'])
                ->name('notifications.delete');
        });

        Route::get('/adopter/resources', [PetResourceController::class, 'index'])->name('adopter.resources');
        Route::get('/resources/{id}', [PetResourceController::class, 'show'])->name('resources.show');

        // About Route
        Route::get('/about', function () {
            return view('Adopter.aboutus');
        })->name('about');

        Route::post('/contact', [AdopterDashboardController::class, 'contactSend'])->name('contact.send');
    });
});

// ---------------------- SHELTER STAFF ROUTES ----------------------
Route::prefix('shelter')->group(function () {

    // Guest-only routes (Login)
    Route::middleware('guest')->group(function () {
        // Login routes
        Route::get('/login', [ShelterAuthController::class, 'showLoginForm'])->name('shelter.login');
        
        Route::post('/login', [ShelterAuthController::class, 'login']);

        // Register routes
        Route::get('/register', [ShelterAuthController::class, 'showRegistrationForm'])->name('shelter.register.form');
        Route::post('/register', [ShelterAuthController::class, 'register'])->name('shelter.register');

        // Password reset routes
        Route::get('/forgot-password', [ShelterAuthController::class, 'showForgotPasswordForm'])->name('shelter.password.request');
        Route::post('/forgot-password', [ShelterAuthController::class, 'sendResetLinkEmail'])->name('shelter.password.email');
        Route::get('/reset-password/{token}', [ShelterAuthController::class, 'showResetPasswordForm'])->name('shelter.password.reset');
        Route::post('/reset-password', [ShelterAuthController::class, 'updatePassword'])->name('shelter.password.update');
    });

    // Authenticated-only routes for shelter staff
    Route::middleware(['auth', 'shelter', 'check.restricted'])->group(function () {
        // Home and logout
        Route::get('/home', [ShelterController::class, 'home'])->name('shelter.home');
        Route::post('/logout', [ShelterAuthController::class, 'logout'])->name('shelter_staff.logout');

        Route::get('/change-password-form', [ShelterAuthController::class, 'showChangePasswordForm'])
            ->name('shelter.password.form');
        Route::post('/change-password', [ShelterAuthController::class, 'changePassword'])
            ->name('shelter.change_password');

        // Pet Management Routes
        Route::prefix('/pets')->group(function () {
            Route::get('/manage', [PetManagementController::class, 'managePets'])->name('shelter.pets.manage');
            Route::get('/create', [PetManagementController::class, 'create'])->name('shelter.pets.create');
            Route::post('/store', [PetManagementController::class, 'store'])->name('shelter.pets.store');
            Route::post('/detect-pet-ai', [PetManagementController::class, 'detectAI'])->name('pet.detectAI');
            Route::post('/detect-colors', [PetManagementController::class, 'detectColors'])->name('detect.colors');
            Route::get('/{id}', [PetManagementController::class, 'view'])->name('shelter.pets.view');
            Route::get('/{id}/edit', [PetManagementController::class, 'edit'])->name('shelter.pets.edit');
            Route::match(['put', 'post'], '/{id}/update', [PetManagementController::class, 'update'])->name('shelter.pets.update');
            Route::delete('/{id}', [PetManagementController::class, 'destroy'])
                ->name('shelter.pets.destroy');
            Route::delete('/image/{id}', [PetManagementController::class, 'deleteImage'])->name('shelter.pets.deleteimage');
        });

        // Adoption Request Routes
        Route::prefix('/adoptions')->group(function () {
            Route::get('/', [ShelterController::class, 'adoptionRequests'])->name('shelter.adoptions');
            Route::put('/{id}', [ShelterController::class, 'updateAdoptionStatus'])->name('shelter.adoption.update');
            Route::post('/approve/{id}', [ShelterController::class, 'approveAdoption'])->name('shelter.approve_adoption');
            Route::post('/{ApplicationID}/reject', [ShelterController::class, 'rejectAdoption'])->name('shelter.reject_adoption');
        });

        // Pet Health Record Routes
        Route::prefix('/health')->group(function () {
            Route::get('/', [ShelterController::class, 'petHealthRecords'])->name('shelter.health');
            Route::get('/create', [ShelterController::class, 'createHealthRecord'])->name('shelter.health.create');
            Route::post('/store', [ShelterController::class, 'storeHealthRecord'])->name('shelter.health.store');
            Route::get('/{id}/view', [ShelterController::class, 'viewHealthRecord'])->name('shelter.health.view');
            Route::get('/edit/{recordId}', [ShelterController::class, 'editHealthRecord'])->name('shelter.health.edit');
            Route::put('/update/{recordId}', [ShelterController::class, 'updateHealthRecord'])->name('shelter.health.update');
            Route::delete('/health/images/{id}', [ShelterController::class, 'deleteImage'])
                ->name('shelter.health.delete-image');
        });

        Route::prefix('/notifications')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('shelter.notifications');
            Route::post('/store', [NotificationController::class, 'store'])->name('shelter.notifications.store');
            Route::post('/mark-read/{id}', [NotificationController::class, 'markRead'])->name('notifications.markRead');
            Route::get('/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
            Route::get('/edit/{id}', [NotificationController::class, 'editAnnouncement'])->name('shelter.announcements.edit');
            Route::put('/update/{id}', [NotificationController::class, 'updateAnnouncement'])->name('shelter.announcements.update');
        });

        Route::prefix('/resources')->group(function () {
            Route::get('manage', [PetResourceController::class, 'manage'])->name('shelter.resources.manage');
            Route::get('/search', [PetResourceController::class, 'search'])->name('resources.search');
            Route::get('/create', [PetResourceController::class, 'create'])->name('shelter.resources.create');
            Route::post('/store', [PetResourceController::class, 'store'])->name('shelter.resources.store');
            Route::get('/{id}/edit', [PetResourceController::class, 'edit'])->name('shelter.resources.edit');
            Route::put('/{id}', [PetResourceController::class, 'update'])->name('shelter.resources.update');
            Route::delete('{id}', [PetResourceController::class, 'destroy'])->name('shelter.resources.destroy');
        });

        Route::get('/announcements/create', [NotificationController::class, 'createAnnouncement'])->name('shelter.announcements.create');

        // Appointments Route
        Route::get('/appointments', [ShelterAppointmentController::class, 'index'])->name('shelter.appointments');
        Route::get('/appointments/get-timeslots', [ShelterAppointmentController::class, 'getTimeSlots'])
            ->name('shelter.appointments.get-timeslots');
        Route::get('/appointments/create', [ShelterAppointmentController::class, 'create'])
            ->name('shelter.appointment.create');
        Route::post('/appointments', [ShelterAppointmentController::class, 'store'])
            ->name('shelter.appointment.store');
        Route::get('/appointments/{appointment}/reschedule', [ShelterAppointmentController::class, 'edit'])
            ->name('shelter.appointment.reschedule.form');
        Route::put('/appointments/{appointment}/reschedule', [ShelterAppointmentController::class, 'reschedule'])
            ->name('shelter.appointment.reschedule');
        Route::post('/appointments/{appointment}/cancel', [ShelterAppointmentController::class, 'cancel'])
            ->name('shelter.appointment.cancel');
        Route::get('/appointments/success/{appointmentID}', [ShelterAppointmentController::class, 'success'])
            ->name('shelter.appointment.success');
        Route::get('/emergency-override/{appointment}', [ShelterAppointmentController::class, 'emergencyOverride'])
            ->name('shelter.appointment.emergency.override');
        Route::get('/contact-adopter/{appointment}', [ShelterAppointmentController::class, 'contactAdopter'])
            ->name('shelter.appointment.contact.adopter');
    });
});

// ----------------------- PROFILE ROUTES -------------------------
Route::middleware(['auth', 'check.restricted'])->group(function () {
    Route::get('/profile', [UserProfileController::class, 'show'])->name('profile.view');

    // Separate routes for different roles
    Route::prefix('adopter')->middleware('auth')->group(function () {
        Route::get('/profile', [UserProfileController::class, 'showAdopterProfile'])->name('adopter.profile.view');
        Route::get('/profile/edit', [UserProfileController::class, 'editAdopterProfile'])->name('adopter.profile.edit');
        Route::put('/profile/update', [UserProfileController::class, 'updateAdopterProfile'])->name('adopter.profile.update');
        Route::get('/adopter/profile/{id}/brief', [UserProfileController::class, 'showAdopterBriefProfile'])
            ->name('adopter.profile.brief');
    });

    Route::prefix('shelter')->middleware('auth')->group(function () {
        Route::get('/profile', [UserProfileController::class, 'showShelterProfile'])->name('shelter.profile.view');
        Route::get('/profile/edit', [UserProfileController::class, 'editShelterProfile'])->name('shelter.profile.edit');
        Route::put('/profile/update', [UserProfileController::class, 'updateShelterProfile'])->name('shelter.profile.update');
    });
});

Route::middleware(['auth', 'check.restricted'])->group(function () {
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/list', [MessageController::class, 'messageList'])->name('messages.list');
    Route::get('/messages/chat/{receiverId}', [MessageController::class, 'chat'])->name('messages.chat');
    Route::post('/messages/send/{receiverId}', [MessageController::class, 'sendMessage'])->name('messages.send');
    Route::get('/video-call/adopter/{receiverId}', [MessageController::class, 'videoCallAdopter'])->name('video.call.adopter');
    Route::get('/video-call/shelter/{receiverId}', [MessageController::class, 'videoCallShelter'])->name('video.call.shelter');
    Route::get('/video-call/{receiverId}', function ($receiverId) {
        $user = Auth::user();

        // Ensure the receiver exists
        $receiver = User::find($receiverId);
        if (!$receiver) {
            return redirect()->back()->with('error', 'User not found.');
        }

        // Redirect based on user role
        if ($user->role === 'adopter') {
            return view('Adopter.video_call', compact('user', 'receiver'));
        } elseif ($user->role === 'shelter_staff') {
            return view('ShelterStaff.video_call', compact('user', 'receiver'));
        } else {
            return abort(403, 'Unauthorized access.');
        }
    })->middleware('auth')->name('video.call');
});

Route::get('/about', function () {
    return view('Adopter.aboutus');
})->name('about');
