<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ShelterMiddleware {

    public function handle(Request $request, Closure $next) {
//        // Check if the user is authenticated and has the correct role
//        if (!Auth::check() || Auth::user()->role !== 'shelter_staff') {
//            abort(403, 'Unauthorized action.');
//        }

        return $next($request);
    }
}
