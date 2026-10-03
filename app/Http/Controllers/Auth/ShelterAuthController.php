<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\ShelterStaffProfile;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ShelterAuthController extends Controller {

    public function showLoginForm() {
        //return 'Shelter login route reached';
        return view('auth.shelter_login');
    }

    public function showRegistrationForm() {
        return view('auth.shelter_register');
    }

    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user(); 
            if ($user->role === 'restricted') {
                Auth::logout(); 
                return redirect()->route('shelter.login')->withErrors([
                            'email' => 'Your account has been restricted. Please contact support.',
                ]);
            }
            if ($user->role !== 'shelter_staff') {
                Auth::logout();
                return redirect()->route('shelter.login')->withErrors([
                            'email' => 'Your account is not authorized to log in as shelter staff.',
                ]);
            }
            $request->session()->regenerate();
            session(['user_id' => $user->id]);
            return redirect()->route('shelter.home');
        }
        return back()->withErrors([
                    'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('shelter.login')->with('success', 'You have been logged out successfully.');
    }

    public function register(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => [
                'required',
                'string',
                'min:8', 
                'regex:/[a-z]/', 
                'regex:/[A-Z]/', 
                'regex:/[0-9]/', 
                'regex:/[@$!%*#?&]/', 
                'confirmed'
            ],
            'gender' => 'required|in:Male,Female,Prefer not to say',
            'phone_number' => 'required|string|max:15',
            'shelter_name' => 'required|string|max:255',
            'shelter_address' => 'required|string',
            'profile_picture' => 'nullable|image|mimes:jpeg,png|max:2048',
            'business_license' => 'required|file|mimes:pdf,jpg,png|max:2048',
            'position' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
        ]);

        Log::info('Received Form Data:', $request->except(['password', 'password_confirmation']));

        $profilePicturePath = null;
        if ($request->hasFile('profile_picture')) {
            $profilePictureName = time() . '_profile_picture.' . $request->file('profile_picture')->getClientOriginalExtension();
            $request->file('profile_picture')->move(public_path('images'), $profilePictureName);
            $profilePicturePath = 'images/' . $profilePictureName;
            Log::info('✅ Profile picture saved to: ' . $profilePicturePath);
        }

        $businessLicensePath = null;
        if ($request->hasFile('business_license')) {
            $businessLicenseName = time() . '_business_license.' . $request->file('business_license')->getClientOriginalExtension();
            $request->file('business_license')->move(public_path('images'), $businessLicenseName);
            $businessLicensePath = 'images/' . $businessLicenseName;
            Log::info('✅ Business license saved to: ' . $businessLicensePath);
        }

        Log::info('📂 File Paths:', [
            'Profile Picture' => $profilePicturePath,
            'Business License' => $businessLicensePath,
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'shelter_staff',
        ]);

        Log::info('✅ Generated UserID: ' . $user->id); 
        $shelterProfile = $user->shelterStaffProfile()->create([
            'phone_number' => $request->phone_number,
            'gender' => $request->gender,
            'shelter_name' => $request->shelter_name,
            'shelter_address' => $request->shelter_address,
            'position' => $request->position,
            'bio' => $request->bio,
            'business_license' => $businessLicensePath,
            'profile_picture' => $profilePicturePath,
        ]);

        Log::info('✅ Shelter profile created:', $shelterProfile->toArray());

        Auth::login($user);
        return redirect()->route('shelter.home')->with('success', 'Registration successful!');
    }

    public function showForgotPasswordForm() {
        return view('auth.s_forgot_password');
    }

    public function sendResetLinkEmail(Request $request) {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
                $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT ? back()->with(['status' => __($status)]) : back()->withErrors(['email' => __($status)]);
    }

    public function showResetPasswordForm($token) {
        return view('auth.s_reset_password', ['token' => $token]);
    }

    public function showChangePasswordForm() {
        return view('shelterstaff.profile.change_password');
    }

    public function updatePassword(Request $request) {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => [
                'required',
                'string',
                'min:8', 
                'regex:/[a-z]/', 
                'regex:/[A-Z]/', 
                'regex:/[0-9]/', 
                'regex:/[@$!%*#?&]/', 
                'confirmed'
            ],
        ]);

        $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    $user->forceFill([
                        'password' => Hash::make($password)
                    ])->save();
                }
        );

        return $status === Password::PASSWORD_RESET ? redirect()->route('shelter.login')->with('status', __($status)) : back()->withErrors(['email' => [__($status)]]);
    }

    public function changePassword(Request $request) {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'new_password' => [
                'required',
                'string',
                'min:8', 
                'regex:/[a-z]/', 
                'regex:/[A-Z]/', 
                'regex:/[0-9]/', 
                'regex:/[@$!%*#?&]/', 
                'confirmed'
            ],
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        // Update the password
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        // Logout the user after password change
        Auth::logout();

        return redirect()->route('shelter.login')->with('success', 'Password updated successfully. Please log in again.');
    }
}
