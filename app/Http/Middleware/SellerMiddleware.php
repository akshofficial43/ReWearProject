<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SellerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Since we've updated our app to allow all users to be sellers,
        // we'll simply check if the user is authenticated
        if (Auth::check()) {
            return $next($request);
        }
        
        return redirect()->route('login')->with('error', 'You must be logged in to access this page.');
    }
}