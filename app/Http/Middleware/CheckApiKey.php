<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->headers->has('X-api-key')) {
            return response()->json(['message' => 'The header X-Api-Key is required.'], 401);
        }
        if ($request->header('X-Api-Key') !== config('app.api_key')) {
            return response()->json(['message' => 'Unauthorized API Key'], 401);
        }
        return $next($request);
    }
}
