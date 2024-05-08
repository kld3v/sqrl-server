<?php

namespace App\Http\Middleware;

use Closure;

class CheckPortalApiKey
{
    public function handle($request, Closure $next)
    {
        $apiKey = $request->header('X-API-Key');
        if ($apiKey !== env('PORTAL_API_KEY')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
