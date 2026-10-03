<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;


class AppServiceProvider extends ServiceProvider {

    /**
     * Register any application services.
     */
    public function register(): void {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {
        Validator::extend('postcode_state', function ($attribute, $value, $parameters, $validator) {
            $data = $validator->getData();
            $state = $data['State'] ?? null;

            // Define your state-to-regex mapping here
            $mappings = [
                "Johor" => '/^7\d{4}$/',
                "Kedah" => '/^05\d{3}$/', // Adjust as necessary
                "Kelantan" => '/^15\d{3}$/',
                "Melaka" => '/^75\d{3}$/',
                "Negeri Sembilan" => '/^71\d{3}$/',
                "Pahang" => '/^25\d{3}$/',
                "Perak" => '/^30\d{3}$/',
                "Perlis" => '/^02\d{3}$/',
                "Penang" => '/^10\d{3}$/',
                "Selangor" => '/^40\d{3}$/',
                "Terengganu" => '/^21\d{3}$/'
            ];

            if (!$state || !isset($mappings[$state])) {
                return false; // Cannot validate without a state
            }

            return preg_match($mappings[$state], $value);
        }, 'The postcode does not match the selected state.');
    }
}
