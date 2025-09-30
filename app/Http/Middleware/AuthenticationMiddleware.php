<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Sanctum\PersonalAccessToken;

class AuthenticationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
       
        $token = $request->bearerToken();

        if (! $token) {
            return response()->json(['error' => 'No token provided'], 401);
        }

        $accessToken = PersonalAccessToken::findToken($token);

        if (! $accessToken) {
            return response()->json(['error' => 'Invalid token'], 401);
        }

        if ($accessToken->created_at->lt(now()->subDays(30))) {
            return response()->json(['error' => 'Token expired'], 401);
        }

       
        $request->attributes->set('accessToken', $accessToken);

        // resolve the authenticated user
        $request->setUserResolver(fn () => $accessToken->tokenable);

        return $next($request);



    }
}
