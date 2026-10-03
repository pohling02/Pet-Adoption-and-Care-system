<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        // If request is expecting JSON (API calls), do not redirect
        if (!$request->expectsJson()) {
            
            // Redirect Shelter Staff to Shelter Login Page
            if ($request->is('shelter_staff/*')) {
                return route('shelter.login');
            }

            // Redirect Admin Users to Admin Login Page
            if ($request->is('admin/*')) {
                return route('admin.login');
            }

            // Redirect Adopters to Adopter Login Page
            if ($request->is('adopter/*')) {
                return route('adopter.login');
            }

            // Default login route for all other users
            return route('login');
        }
    }
}
