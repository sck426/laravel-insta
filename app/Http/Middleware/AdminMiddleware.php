<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Auth::check() - to check if the user is logged in from the site
        if(Auth::check() && Auth::user()->role_id == User::ADMIN_ROLE_ID){
            return $next($request); // redirect to next page (admin page)
        }else {
            return redirect()->back();
        }
    }
}
