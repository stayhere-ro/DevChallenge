<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class HttpResponse
{
    public static function simpleResponse(int $code, ?string $message = null): JsonResponse
    {
        return response()->json(['message' => $message ?? self::getDefaultMessage($code)], $code);
    }

    public static function dataResponse(int $code, array $data, ?string $message = null): JsonResponse
    {
        return response()->json([
            'message' => $message ?? self::getDefaultMessage($code),
            'data' => $data
        ], $code);
    }

    private static function getDefaultMessage(int $code): string
    {
        return match ($code) {
            200 => 'Success',
            404 => 'Not Found',
            500 => 'Internal Server Error',
            default => 'Internal Server Error'
        };
    }
}
