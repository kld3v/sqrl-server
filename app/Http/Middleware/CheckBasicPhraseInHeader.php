<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckBasicPhraseInHeader
{

    public function handle($request, Closure $next)
    {
        $phrase = $request->header('Lowri');

        if ($phrase !== 'smells') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
