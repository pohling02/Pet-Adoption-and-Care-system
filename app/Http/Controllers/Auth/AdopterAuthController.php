<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\AdopterProfile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;

class AdopterAuthController extends Controller {

    public function showLoginForm() {
        return view('auth.adopter_login');
    }

    public function showRegisterForm() {
        return view('auth.adopter_register');
    }

    public function register(Request $request) {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users'],
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
            'gender' => ['required', 'string', 'in:Male,Female,Prefer not to say'],
            'phone_number' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string'],
            'occupation' => ['required', 'string', 'max:255'],
            'preferred_location' => ['required', 'string', 'in:Johor,Kedah,Kelantan,Kuala Lumpur,Melaka,Negeri Sembilan,Pahang,Penang,Perak,Perlis,Putrajaya,Selangor,Terengganu,Multiple Locations,Any Location'],
            'pet_preference' => ['required', 'string', 'max:255'],
            'bio' => ['required', 'string'],
            'profile_picture' => ['required', 'image', 'mimes:jpeg,png', 'max:2048'],
            'home_type' => ['required', 'string', 'in:Apartment / Condo, House with yard, Farm / large property'],
            'other_pets' => ['required', 'string', 'in:Cats, Dogs, Other'],
            'allergies' => ['required', 'string', 'in:Yes, No'],
            'hours_per_day'=> ['required', 'numeric'],
            //'g-recaptcha-response' => ['required'],
        ]);

        // $recaptchaResponse = $request->input('g-recaptcha-response');
        // $recaptchaSecret = env('RECAPTCHA_SECRET_KEY');
        // $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$recaptchaSecret}&response={$recaptchaResponse}");
        // $responseData = json_decode($response);
        // if (!$responseData->success) {
        //     return back()->withErrors(['captcha' => 'reCAPTCHA verification failed. Please try again.']);
        // }

        $profilePicturePath = null;
        if ($request->hasFile('profile_picture')) {
            $profilePictureName = time() . '_profile_picture.' . $request->file('profile_picture')->getClientOriginalExtension();
            $request->file('profile_picture')->move(public_path('images'), $profilePictureName);
            $profilePicturePath = 'images/' . $profilePictureName;
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'adopter',
        ]);

        AdopterProfile::create([
            'UserID' => $user->UserID,
            'gender' => $request->gender === '' ? null : $request->gender,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'occupation' => $request->occupation,
            'pet_preference' => $request->pet_preference,
            'preferred_location' => $request->preferred_location,
            'bio' => $request->bio,
            'profile_picture' => $profilePicturePath,
        ]);

        Auth::login($user);
        return redirect()->route('adopter.profile.view')->with('success', 'Registration successful!');
    }

    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if ($user->role === 'restricted') {
                Auth::logout();
                return redirect()->route('adopter.login')->withErrors([
                            'email' => 'Your account has been restricted. Please contact our support team at support@example.com for assistance.',
                ]);
            }

            if ($user->role !== 'adopter') {
                Auth::logout();
                return redirect()->route('adopter.login')->withErrors([
                            'email' => 'Your account is not authorized to login as an adopter. Please contact support.',
                ]);
            }

            $request->session()->regenerate();
            return redirect()->intended(route('adopter.home'));
        }
        return back()->withErrors(['email' => 'Authentication failed!']);
    }

    public function logout(Request $request) {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken(); // Prevent CSRF issues

        return redirect()->route('adopter.login')->with('success', 'Logged out successfully!');
    }

    public function showForgotPasswordForm() {
        return view('Auth.a_forgot_password');
    }

    public function sendResetLinkEmail(Request $request) {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT ? back()->with('status', __('A password reset link has been sent to your email.')) : back()->withErrors(['email' => __('Unable to send reset link.')]);
    }

    public function showResetPasswordForm(Request $request, $token) {
        return view('Auth.a_reset_password', [
            'token' => $token,
            'email' => $request->email // Ensure the email is passed to the view
        ]);
    }

    public function resetPassword(Request $request) {
        $request->validate([
            'email' => 'required|email|exists:users,email',
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
            'token' => 'required'
        ]);

        $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    $user->forceFill([
                        'password' => Hash::make($password)
                    ])->save();
                }
        );

        return $status === Password::PASSWORD_RESET ? redirect()->route('adopter.login')->with('success', __('Your password has been reset successfully.')) : back()->withErrors(['email' => __('Failed to reset password.')]);
    }

    public function showChangePasswordForm() {
        return view('Adopter.profile.change_password');
    }

    public function updatePassword(Request $request) {
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

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Your current password is incorrect.');
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        Auth::logout();
        return redirect()->route('adopter.login')->with('success', 'Password successfully updated. Please log in again.');
    }
}
