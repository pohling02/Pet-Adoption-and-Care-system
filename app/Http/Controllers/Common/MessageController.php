<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Pet;
use App\Models\Message;
use App\Models\AdopterProfile;
use App\Models\ShelterStaffProfile;

class MessageController extends Controller {

    public function index() {
        if (!Auth::check()) {
            return redirect()->route('adopter.login')->with('error', 'Please login to view messages.');
        }

        $user = Auth::user();

        $conversations = Message::where('SenderID', $user->UserID)
                ->orWhere('ReceiverID', $user->UserID)
                ->with(['sender', 'receiver'])
                ->latest()
                ->get();

        $contacts = collect();

        if ($user->role === 'adopter') {
            $contacts = $conversations->map(function ($message) use ($user) {
                        return $message->SenderID == $user->UserID ? $message->receiver : $message->sender;
                    })->unique('UserID')->filter();
        } elseif ($user->role === 'shelter_staff') {
            $contacts = $conversations->map(function ($message) use ($user) {
                        return $message->SenderID == $user->UserID ? $message->receiver : $message->sender;
                    })->filter(function ($contact) {
                        return $contact && $contact->role === 'adopter';
                    })->unique('UserID');
        }
        return view('ShelterStaff.message.message_center', compact('contacts', 'conversations'));
    }

    public function chat(Request $request, $receiverId = null) {
        if (!Auth::check()) {
            return redirect()->route('adopter.login')->with('error', 'Please login to view messages.');
        }
        $user = Auth::user();
        if ($user->role === 'adopter' && $request->has('pet_id')) {
            $pet = Pet::find($request->input('pet_id'));

            if (!$pet) {
                return redirect()->back()->with('error', 'Pet not found. Unable to send message.');
            }
            $receiverId = $pet->ShelterID;
        } elseif ($user->role === 'shelter_staff' && !$receiverId) {
            return redirect()->route('messages.index')->with('error', 'No adopter selected.');
        }
        $receiver = User::find($receiverId);
        if (!$receiver) {
            return redirect()->route('messages.index')->with('error', 'User not found.');
        }
        $messages = Message::where(function ($query) use ($user, $receiverId) {
                    $query->where('SenderID', $user->UserID)->where('ReceiverID', $receiverId);
                })
                ->orWhere(function ($query) use ($user, $receiverId) {
                    $query->where('SenderID', $receiverId)->where('ReceiverID', $user->UserID);
                })
                ->orderBy('created_at', 'asc')
                ->get();
        Message::where('SenderID', $receiverId)
                ->where('ReceiverID', $user->UserID)
                ->update(['is_read' => true]);

        if ($user->role === 'adopter') {
            return view('Adopter.message.messages', compact('receiver', 'messages'));
        } elseif ($user->role === 'shelter_staff') {
            return view('ShelterStaff.message.message_detail', compact('receiver', 'messages'));
        } else {
            return abort(403, 'Unauthorized access.');
        }
    }

    public function sendMessage(Request $request, $receiverId = null) {
        $user = Auth::user();
        $request->validate([
            'Content' => 'nullable|string|max:500',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,mov,pdf,doc,docx,txt|max:5120',
        ]);

        if ($user->role === 'adopter' && !$receiverId) {
            if (!$request->has('pet_id')) {
                return redirect()->back()->with('error', 'No pet selected. Unable to send message.');
            }

            $pet = Pet::find($request->input('pet_id'));
            if (!$pet) {
                return redirect()->back()->with('error', 'Pet not found. Unable to send message.');
            }

            $receiverId = $pet->ShelterID;
        } elseif ($user->role === 'shelter_staff' && !$receiverId) {
            return redirect()->back()->with('error', 'No adopter selected. Unable to send message.');
        }

        $filePath = null;
        $fileType = null;
        $originalFileName = null;
        $storedFileName = null; 

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $originalFileName = $file->getClientOriginalName(); 
            $fileType = $file->getClientOriginalExtension(); 
            $storedFileName = time() . '_' . uniqid() . '.' . $fileType;
            $filePath = $file->storeAs('uploads/messages', $storedFileName, 'public');
            \Log::info("File uploaded: Original Name - $originalFileName | Stored Name - $storedFileName | Path - $filePath");
        }

        if (!$request->input('Content') && !$filePath) {
            return redirect()->back()->with('error', 'Message cannot be empty.');
        }

        $message = Message::create([
            'SenderID' => $user->UserID,
            'ReceiverID' => $receiverId,
            'Content' => $request->input('Content') ?? '',
            'file_path' => $filePath,
            'file_name' => $originalFileName ?? $storedFileName, 
            'file_type' => $fileType,
            'is_read' => false,
        ]);

        \Log::info('Message saved to database: ' . json_encode($message));

        return redirect()->route('messages.chat', ['receiverId' => $receiverId])
                        ->with('success', 'Message sent successfully!');
    }

    public function messageList() {
        if (!Auth::check()) {
            return redirect()->route('adopter.login')->with('error', 'Please login to view messages.');
        }
        $user = Auth::user();
        if ($user->role === 'adopter') {
            $profile = AdopterProfile::firstOrCreate(
                    ['UserID' => $user->UserID],
                    ['profile_picture' => 'images/blankprofile.jpg'] // Default image if none exists
            );
        } elseif ($user->role === 'shelter_staff') {
            $profile = ShelterStaffProfile::firstOrCreate(
                    ['UserID' => $user->UserID],
                    ['profile_picture' => 'images/blankprofile.jpg']
            );
        } else {
            return abort(403, 'Unauthorized action.');
        }
        $conversations = Message::where(function ($query) use ($user) {
                    $query->where('SenderID', $user->UserID)
                            ->orWhere('ReceiverID', $user->UserID);
                })
                ->with(['sender', 'receiver'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->groupBy(function ($message) use ($user) {
                    return $message->SenderID == $user->UserID ? $message->ReceiverID : $message->SenderID;
                });
        \Log::info('User ' . $user->UserID . ' conversations: ' . $conversations->count());

        if ($user->role === 'adopter') {
            return view('Adopter.message.message_list', compact('conversations', 'profile'));
        } elseif ($user->role === 'shelter_staff') {
            return view('ShelterStaff.message.message_center', compact('conversations', 'profile'));
        } else {
            return abort(403, 'Unauthorized action.');
        }
    }
}
