<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserRestricted
{
    public function handle(Request $request, Closure $next)
    {
        // Skip this middleware for login-related routes and public routes
        if ($request->routeIs('login') || 
            $request->routeIs('adopter.login') || 
            $request->routeIs('shelter.login') || 
            $request->routeIs('admin.login') ||
            $request->routeIs('adopter.register.form') ||
            $request->routeIs('adopter.register') ||
            $request->routeIs('shelter.register.form') ||
            $request->routeIs('shelter.register') ||
            $request->is('/') ||
            $request->is('login') ||
            $request->is('about') ||
            $request->is('faq') ||
            $request->is('contact') ||
            $request->is('privacy-policy') ||
            $request->is('terms-of-service') ||
            $request->is('admin/login') ||
            $request->is('adopter/login') ||
            $request->is('adopter/register') ||
            $request->is('shelter/login') ||
            $request->is('shelter/register') ||
            strpos($request->path(), 'password/reset') !== false ||
            strpos($request->path(), 'forgot-password') !== false) {
            return $next($request);
        }
        
        if (Auth::check() && Auth::user()->role === 'restricted') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect()->route('login')
                ->with('error', 'Your account has been restricted. Please contact administrator for assistance.');
        }
        
        return $next($request);
    }
}