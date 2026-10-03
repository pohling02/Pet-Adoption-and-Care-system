<?php

namespace App\Http\Controllers\Adopter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Pet;
use App\Models\PetImage;
use App\Models\AdopterProfile;
use App\Services\OpenAIService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;

class AdopterDashboardController extends Controller
{

    public function index()
    {
        $userID = Auth::id();
        Log::info("Fetching adopter profile for UserID: $userID");

        $adopterProfile = AdopterProfile::where('UserID', $userID)->first();

        if (!$adopterProfile) {
            return redirect()->route('adopter.profile.view')->with('error', 'Profile not found. Please set up your profile.');
        }

        $adopterProfile->refresh();

        $potentialMatches = Pet::with('images')
            ->where('AdoptionStatus', 'available')
            ->get();

        $petScores = OpenAIService::getPersonalizedRecommendations($adopterProfile, $potentialMatches);
        $recommendedPets = collect();
        try {
            foreach ($petScores as $petID => $score) {
                $pet = $potentialMatches->firstWhere('PetID', $petID);

                if ($pet) {
                    $pet->recommendationReason = "This pet matches your profile based on energy, personality, and lifestyle preferences.";

                    try {
                        // Call OpenAI safely — won’t block dashboard if fails
                        $pet->enhancedPersonality = OpenAIService::getPetPersonality($pet);
                    } catch (\Exception $e) {
                        Log::warning("Failed to get personality for PetID: {$pet->PetID}. " . $e->getMessage());
                        $pet->enhancedPersonality = $pet->Personality ?? 'Friendly and unique personality.';
                    }

                    $recommendedPets->push($pet);
                }
            }

            // If no recommended pets found, fallback to top 4
            if ($recommendedPets->isEmpty()) {
                $pets = Pet::with('images')->orderBy('PetID', 'asc')->take(4)->get();
            } else {
                $pets = $recommendedPets;
            }
        } catch (\Exception $e) {
            Log::error('Error generating recommendations: ' . $e->getMessage());
            // Fallback if outer loop or OpenAI failed completely
            $pets = Pet::with('images')->orderBy('PetID', 'asc')->take(4)->get();
        }



        return view('Adopter.dashboard', compact('pets', 'recommendedPets'));
    }


    public function adopterHome()
    {
        $pets = Pet::with('images')->orderBy('PetID', 'asc')->get();
        Log::info('All pets fetched for adopter home:', $pets->toArray());
        if ($pets->isEmpty()) {
            Log::warning('No pets found in the database for adopter home.');
        }
        return view('Adopter.dashboard', compact('pets'));
    }

    public function showAdoptionPage(Request $request)
    {
        $search = $request->input('search');
        $availableStates = $request->input('state', []);
        $petCategories = $request->input('category', []);
        $selectedGenders = $request->input('gender', []);
        $selectedAges = $request->input('age', []);
        $sort = $request->input('sort', 'newest'); // Default sort is 'newest'

        $query = Pet::with('images')
            ->whereDoesntHave('adoptionApplications', function ($query) {
                $query->where('AdoptionStatus', 'Approved');
            })
            ->when($search, function ($query, $search) {
                return $query->where(function ($subQuery) use ($search) {
                    $subQuery->whereRaw('LOWER(PetName) LIKE ?', ['%' . strtolower($search) . '%'])
                        ->orWhereRaw('LOWER(Species) LIKE ?', ['%' . strtolower($search) . '%'])
                        ->orWhereRaw('LOWER(Breed) LIKE ?', ['%' . strtolower($search) . '%'])
                        ->orWhereRaw('LOWER(Color) LIKE ?', ['%' . strtolower($search) . '%'])
                        ->orWhereRaw('LOWER(Personality) LIKE ?', ['%' . strtolower($search) . '%']);
                });
            })
            ->when(!empty($availableStates), function ($query) use ($availableStates) {
                return $query->whereIn('CurrentLocation', $availableStates);
            })
            ->when(!empty($petCategories), function ($query) use ($petCategories) {
                return $query->whereIn('Species', $petCategories);
            })
            ->when(!empty($selectedGenders), function ($query) use ($selectedGenders) {
                return $query->whereIn('Gender', $selectedGenders);
            })
            ->when(!empty($selectedAges), function ($query) use ($selectedAges) {
                return $query->where(function ($query) use ($selectedAges) {
                    foreach ($selectedAges as $age) {
                        if ($age === 'baby') {
                            $query->orWhereRaw("TIMESTAMPDIFF(YEAR, DateOfBirth, CURDATE()) < 1");
                        } elseif ($age === 'young') {
                            $query->orWhereRaw("TIMESTAMPDIFF(YEAR, DateOfBirth, CURDATE()) BETWEEN 1 AND 3");
                        } elseif ($age === 'adult') {
                            $query->orWhereRaw("TIMESTAMPDIFF(YEAR, DateOfBirth, CURDATE()) BETWEEN 4 AND 8");
                        } elseif ($age === 'senior') {
                            $query->orWhereRaw("TIMESTAMPDIFF(YEAR, DateOfBirth, CURDATE()) >= 9");
                        }
                    }
                });
            })
            ->where('AdoptionStatus', 'Available');

        // Apply sorting
        switch ($sort) {
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'name-asc':
                $query->orderBy('PetName', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        Log::info("SQL Query: " . $query->toSql(), $query->getBindings());
        $pets = $query->get();
        return view('Adopter.pets.pet_list', compact('pets'));
    }


    public function faq()
    {
        return view('Adopter.faq');
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'message' => 'required|string|max:2000',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'messageContent' => nl2br(e($request->message)),
        ];

        Mail::send('emails.contact', $data, function ($mail) use ($request) {
            $mail->to('support@petopia.com')
                ->subject("New Contact Message from {$request->name}")
                ->replyTo($request->email);
        });

        return back()->with('success', 'Your message has been sent successfully!');
    }
}
