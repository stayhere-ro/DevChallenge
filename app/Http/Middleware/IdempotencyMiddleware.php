<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class IdempotencyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        // 1. If no idempotency key, proceed with controller logic
        if(!$request->hasHeader('Idempotency-Key')) {
            return $next($request);
        }

        $idempotencyKey = $request->header('Idempotency-Key');


        // Add prefix to avoid collision with other cache entries
        $cacheKey = "idempotency_key:{$idempotencyKey}";

        // 2. Check if a response is already stored for this cache key, and return it
        if($cachedResponse = Cache::get($cacheKey)) {
            return response()->json($cachedResponse['content'], $cachedResponse['status']);
        }

        // 3. Process the request if no cache hit was found
        $response = $next($request);

        // 4. If the response was successful cache the key
        if($response->isSuccessful()){
            Cache::put($cacheKey, [
                'content' => $response->original,
                'status' => $response->getStatusCode()
            ], now()->addHours(10));
        }

        return $response;

    }
}
