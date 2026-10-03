<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\ConnectionException;
use App\Models\Pet;
use App\Models\AdopterProfile;

class OpenAIService
{
    /** --------------------------------------------------
     *  Get synonyms for a word
     *  -------------------------------------------------- */
    public static function getSynonyms($word)
    {
        return Cache::remember("synonyms_$word", now()->addDays(1), function () use ($word) {
            $apiKey = env('OPENAI_API_KEY');
            $url = "https://api.openai.com/v1/completions";

            try {
                $response = Http::timeout(15)
                    ->withHeaders([
                        'Authorization' => "Bearer $apiKey",
                        'Content-Type' => 'application/json',
                    ])
                    ->post($url, [
                        'model' => 'gpt-3.5-turbo-instruct',
                        'prompt' => "Provide a list of synonyms for the word '$word' separated by commas.",
                        'max_tokens' => 20,
                        'temperature' => 0.7
                    ]);

                if ($response->ok() && isset($response->json()['choices'][0]['text'])) {
                    $text = trim($response->json()['choices'][0]['text']);
                    return array_map('strtolower', preg_split('/,\s*/', $text));
                }

                Log::warning("OpenAI synonym API failed for '$word'", ['response' => $response->body()]);
            } catch (ConnectionException $e) {
                Log::error("Connection error fetching synonyms: " . $e->getMessage());
            }

            return [$word]; // fallback
        });
    }

    /** --------------------------------------------------
     *  Generate descriptive pet personality text
     *  -------------------------------------------------- */
    public static function getPetPersonality($petId)
    {
        return Cache::remember("pet_personality_{$petId}", now()->addDays(7), function () use ($petId) {
            $pet = Pet::find($petId);
            if (!$pet || empty($pet->Personality)) {
                return null;
            }

            $apiKey = env('OPENAI_API_KEY');
            $url = "https://api.openai.com/v1/chat/completions";

            $petInfo = [
                'name' => $pet->PetName,
                'species' => $pet->Species,
                'breed' => $pet->Breed,
                'gender' => $pet->Gender,
                'age' => now()->diffInYears($pet->DateOfBirth),
                'personality' => $pet->Personality,
                'energy_level' => $pet->EnergyLevel,
                'friendliness' => $pet->Friendliness,
                'adaptability' => $pet->Adaptability
            ];

            $prompt = "Given this pet info, write a 2–3 sentence engaging personality description (no health info):\n\n" . json_encode($petInfo);

            try {
                $response = Http::timeout(20)
                    ->withHeaders([
                        'Authorization' => "Bearer $apiKey",
                        'Content-Type' => 'application/json',
                    ])
                    ->post($url, [
                        'model' => 'gpt-3.5-turbo',
                        'messages' => [
                            ['role' => 'system', 'content' => 'You are an animal personality specialist.'],
                            ['role' => 'user', 'content' => $prompt],
                        ],
                        'temperature' => 0.7,
                        'max_tokens' => 150
                    ]);

                if ($response->ok() && isset($response->json()['choices'][0]['message']['content'])) {
                    return $response->json()['choices'][0]['message']['content'];
                }

                Log::warning("OpenAI pet personality API failed for pet $petId", ['response' => $response->body()]);
            } catch (ConnectionException $e) {
                Log::error("Connection error fetching pet personality: " . $e->getMessage());
            }

            return $pet->Personality;
        });
    }

    /** --------------------------------------------------
     *  Create text embedding (with retry & timeout)
     *  -------------------------------------------------- */
    public static function createEmbedding($text)
    {
        return Cache::remember("embedding_" . md5($text), now()->addDays(7), function () use ($text) {
            $apiKey = env('OPENAI_API_KEY');
            $url = "https://api.openai.com/v1/embeddings";

            if (empty($text) || !is_string($text)) {
                Log::warning('Invalid text input for embedding.', ['text' => $text]);
                return [];
            }

            $maxRetries = 3;
            $delay = 500; // ms

            for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
                try {
                    $response = Http::timeout(30)
                        ->withHeaders([
                            'Authorization' => "Bearer $apiKey",
                            'Content-Type' => 'application/json',
                        ])
                        ->post($url, [
                            'model' => 'text-embedding-3-large',
                            'input' => $text,
                        ]);

                    if ($response->ok() && isset($response->json()['data'][0]['embedding'])) {
                        return $response->json()['data'][0]['embedding'];
                    }

                    Log::warning("Embedding API failed (Attempt $attempt)", [
                        'status' => $response->status(),
                        'body' => $response->body(),
                        'input_preview' => mb_substr($text, 0, 100)
                    ]);
                } catch (ConnectionException $e) {
                    Log::warning("Embedding connection error (Attempt $attempt): " . $e->getMessage());
                }

                usleep($delay * 1000);
                $delay *= 2; // exponential backoff
            }

            Log::error('Embedding API failed after all retries, using dummy vector.', [
                'input_preview' => mb_substr($text, 0, 100)
            ]);

            return array_fill(0, 1536, 0.0);
        });
    }

    /** --------------------------------------------------
     *  Calculate cosine similarity between vectors
     *  -------------------------------------------------- */
    public static function cosineSimilarity($vec1, $vec2)
    {
        $dot = $normA = $normB = 0.0;
        foreach ($vec1 as $i => $val) {
            $dot += $val * $vec2[$i];
            $normA += $val * $val;
            $normB += $vec2[$i] * $vec2[$i];
        }
        return $dot / (sqrt($normA) * sqrt($normB) + 1e-8);
    }

    /** --------------------------------------------------
     *  Generate personalized pet recommendations
     *  -------------------------------------------------- */
    public static function getPersonalizedRecommendations($adopterProfile, $availablePets)
    {
        $cacheKey = "personalized_recommendations_" . $adopterProfile->UserID;
        Cache::forget($cacheKey);

        return Cache::remember($cacheKey, now()->addHours(1), function () use ($adopterProfile, $availablePets) {
            if (count($availablePets) === 0) {
                return [];
            }

            $adopterInfo = [
                $adopterProfile->gender,
                $adopterProfile->occupation,
                $adopterProfile->pet_preference,
                $adopterProfile->preferred_location,
                $adopterProfile->bio,
                $adopterProfile->activity_level,
                $adopterProfile->home_type,
                $adopterProfile->other_pets,
                $adopterProfile->allergies,
                $adopterProfile->hours_per_day,
                $adopterProfile->personality_preference,
            ];

            $adopterText = implode(' ', array_filter($adopterInfo));
            $adopterEmbedding = self::createEmbedding($adopterText);

            $scores = [];
            foreach ($availablePets as $pet) {
                $petText = implode(' ', [
                    $pet->PetName,
                    $pet->Species,
                    $pet->Breed,
                    $pet->Color,
                    $pet->Gender,
                    now()->diffInYears($pet->DateOfBirth),
                    $pet->Personality,
                    $pet->Background,
                    $pet->CurrentLocation,
                    $pet->EnergyLevel,
                    $pet->Friendliness,
                    $pet->Adaptability
                ]);
                $petEmbedding = self::createEmbedding($petText);
                $scores[$pet->PetID] = self::cosineSimilarity($adopterEmbedding, $petEmbedding);
            }

            arsort($scores);
            return array_slice($scores, 0, 4, true);
        });
    }
}