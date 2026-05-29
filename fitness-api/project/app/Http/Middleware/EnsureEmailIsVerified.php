<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        if (env('DEBUG_BYPASS_EMAIL_VERIFICATION') === true) {
            return $next($request);
        }

        if (! $request->user() || ! $request->user()->email_verified_at) {
            return response()->json(['message' => 'Your email address is not verified.'], 403);
        }

        return $next($request);
    }
}