<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApprovedUser
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && !auth()->user()->is_approved) {
            auth()->logout();
            return redirect()->route('login')->withErrors(['email' => 'Your account is waiting for Super Admin approval.']);
        }
        return $next($request);
    }
}
