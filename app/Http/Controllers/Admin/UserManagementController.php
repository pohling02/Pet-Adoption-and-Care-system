<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class UserManagementController extends Controller {

    public function home() {
        return view('AdminStaff.admin_home');
    }

    public function manageUsers() {
        $users = User::where('role', '!=', 'admin')
                ->with(['adopterProfile', 'shelterStaffProfile'])
                ->get();
        return view('AdminStaff.admin_manage_users', compact('users'));
    }

    public function restrictUser($userId, Request $request) {
        $user = User::findOrFail($userId);
        if ($user->role !== 'admin') {
            $wasRestricted = $user->role !== 'restricted' && $request->role === 'restricted';
            $user->role = ($user->role === 'restricted') ? 'adopter' : 'restricted'; // Toggle
            $user->save();
            if ($user->role === 'restricted') {
                \DB::table('sessions')
                        ->where('user_id', $user->id)
                        ->delete();
            }
            return redirect()->route('admin.manage_users')->with('success', 'User status updated and user has been logged out.');
        }
        return redirect()->back()->with('error', 'Cannot modify admin accounts.');
    }
    
    public function unrestrictUser($userId) {
        $user = User::findOrFail($userId);

        if ($user->role === 'restricted') {
            $user->role = 'adopter'; // Change role back to adopter or any previous role
            $user->save();

            return redirect()->route('admin.manage_users')->with('success', 'User access restored.');
        }

        return redirect()->back()->with('error', 'Invalid operation.');
    }

    public function resetPassword($userId) {
        Log::info('Reset Password triggered for user ID: ' . $userId);
        Log::info('Request Method: ' . request()->method());

        if (request()->method() !== 'POST') {
            return response()->json(['error' => 'Method not allowed'], 405);
        }

        $user = User::findOrFail($userId);

        // Generate a new temporary password
        $temporaryPassword = 'TempPass' . rand(1000, 9999);

        // Update the user's password (hashed)
        $user->password = Hash::make($temporaryPassword);
        $user->save();

        // Send Email to User with Temporary Password
        Mail::to($user->email)->send(new ResetPasswordMail($user, $temporaryPassword));

        return redirect()->route('admin.manage_users')->with('success', 'Password reset successfully! The new password has been sent to the user\'s email.');
    }

    public function deleteUser($userId) {
        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('admin.manage_users')->with('error', 'User not found.');
        }

        if ($user->role === 'admin') {
            return redirect()->back()->with('error', 'Cannot delete admin accounts.');
        }

        try {
            $user->delete();
            return redirect()->route('admin.manage_users')->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.manage_users')->with('error', 'Error deleting user: ' . $e->getMessage());
        }
    }
}
