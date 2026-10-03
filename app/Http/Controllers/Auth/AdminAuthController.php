<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AdminAuthController extends Controller {

    // Show Admin Login Form
    public function showLoginForm() {
        return view('AdminStaff.admin_login');
    }
    
    public function login(Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|string|min:6',
    ]);

    if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
        if (Auth::user()->role !== 'admin') {
            Auth::logout();
            return redirect()->back()->with('error', 'Unauthorized access.');
        }
        return redirect()->route('admin.home');
    }
    return back()->with('error', 'Invalid credentials or unauthorized access.');
}

    public function logout() {
        Auth::logout();
        return redirect()->route('admin.login')->with('success', 'Logged out successfully.');
    }
}
