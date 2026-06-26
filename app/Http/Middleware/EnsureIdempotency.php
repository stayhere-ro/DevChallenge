<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class EnsureIdempotency
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->isMethod('POST')) {
            return $next($request);
        }

        $idempotencyKey = $request->header('Idempotency-Key');

        if (! $idempotencyKey) {
            return $next($request);
        }

        $cacheKey = 'idempotency:'.hash('sha256', $request->path().':'.$idempotencyKey);

        if (Cache::has($cacheKey)) {
            /** @var array{status: int, body: array<string, mixed>} $cached */
            $cached = Cache::get($cacheKey);

            return response()->json($cached['body'], $cached['status']);
        }

        /** @var Response $response */
        $response = $next($request);

        if ($response instanceof JsonResponse && $response->getStatusCode() < 500) {
            Cache::put($cacheKey, [
                'status' => $response->getStatusCode(),
                'body' => $response->getData(true),
            ], config('booking.idempotency_ttl_seconds', 86400));
        }

        return $response;
    }
}
