<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller {

    public function index() {
        $user = Auth::user();

        if ($user->role === 'shelter_staff') {
            $notifications = Notification::whereIn('type', ['announcement', 'adoption_rejection'])
                    ->orderBy('created_at', 'desc')
                    ->get();
        } else {
            $notifications = Notification::where('UserID', $user->UserID)->orderBy('created_at', 'desc')->get();
        }

        $view = ($user->role === 'shelter_staff') ? 'ShelterStaff.notification.index' : 'Adopter.notification.index';

        return view($view, compact('notifications'));
    }

    public function createAnnouncement() {
        $adopters = User::where('role', 'adopter')->get();
        $announcements = Notification::where('type', 'announcement')->orderBy('created_at', 'desc')->get();

        return view('ShelterStaff.notification.create', compact('adopters', 'announcements'));
    }

    public function store(Request $request) {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'recipients' => 'required|string',
        ]);

        if ($request->recipients === 'all_adopters') {
            $users = User::where('role', 'adopter')->get();
        } else {
            $users = User::where('UserID', $request->recipients)->get();
        }

        foreach ($users as $user) {
            Notification::create([
                'UserID' => $user->UserID,
                'type' => 'announcement',
                'message' => $request->subject . ' - ' . $request->message,
            ]);
        }

        return redirect()->route('shelter.notifications')->with('success', 'Announcement sent successfully.');
    }

    public function editAnnouncement($id) {
        $announcement = Notification::findOrFail($id);

        if ($announcement->type !== 'announcement') {
            return redirect()->route('shelter.notifications')->with('error', 'Invalid announcement.');
        }

        return view('ShelterStaff.notification.edit', compact('announcement'));
    }

    public function updateAnnouncement(Request $request, $id) {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $announcement = Notification::findOrFail($id);
        $announcement->message = $request->subject . ' - ' . $request->message;
        $announcement->save();

        return redirect()->route('shelter.notifications')->with('success', 'Announcement updated successfully.');
    }

    public function deleteNotification($id) {
        $notification = Notification::where('NotificationID', $id)
                ->where('UserID', Auth::id()) // Ensure only the owner can delete
                ->first();

        if (!$notification) {
            return redirect()->back()->with('error', 'Notification not found.');
        }

        $notification->delete();

        return redirect()->back()->with('success', 'Notification deleted successfully.');
    }

    public function markRead($id) {
        $user = Auth::user();

        // Allow only adopters to mark notifications as read
        if ($user->role !== 'adopter') {
            return response()->json(['error' => 'Shelter staff cannot mark notifications as read'], 403);
        }

        // Find the notification for the current adopter using the custom primary key
        $notification = Notification::where('NotificationID', $id)
                ->where('UserID', $user->UserID)
                ->firstOrFail();

        // Update the read_at timestamp to mark it as read
        $notification->read_at = now();
        $notification->save();

        return response()->json(['message' => 'Notification marked as read']);
    }

    public function markAllRead() {
        $user = Auth::user();

        // Allow only adopters to mark notifications as read
        if ($user->role !== 'adopter') {
            return redirect()->back()->with('error', 'Shelter staff cannot mark notifications as read');
        }

        // Update all unread notifications for this adopter
        Notification::where('UserID', $user->UserID)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }
}
