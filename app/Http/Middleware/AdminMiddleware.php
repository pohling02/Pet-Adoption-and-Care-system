<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware {

    public function handle(Request $request, Closure $next) {
        // Allow access to the login page without redirecting
        if ($request->routeIs('admin.login')) {
            return $next($request);
        }

        // Check if the user is authenticated
        if (!Auth::check()) {
            return $this->unauthorizedResponse($request);
        }

        // Check if the user is an admin
        if (Auth::user()->role !== 'admin') {
            return $this->unauthorizedResponse($request);
        }

        return $next($request);
    }

    private function unauthorizedResponse($request) {
        // If the request is an AJAX request or DELETE request, return 403
        if ($request->ajax() || $request->wantsJson() || $request->isMethod('DELETE')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Otherwise, redirect non-admins to homepage
        return redirect('/')->with('error', 'Access denied.');
    }
}
