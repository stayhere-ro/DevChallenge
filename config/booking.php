<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Business hours (local app timezone)
    |--------------------------------------------------------------------------
    |
    | Bookings may start at whole hours from `start` inclusive up to but not
    | including `end` as the starting hour (e.g. 8–17 → last slot 16:00).
    |
    */

    'business_hours' => [
        'start' => (int) env('BOOKING_HOUR_START', 8),
        'end' => (int) env('BOOKING_HOUR_END', 17),
    ],

    'slot_duration_minutes' => (int) env('BOOKING_SLOT_MINUTES', 60),

    'availability_cache_ttl_seconds' => (int) env('BOOKING_AVAILABILITY_CACHE_TTL', 45),

    'idempotency_ttl_seconds' => (int) env('BOOKING_IDEMPOTENCY_TTL', 86400),

];
