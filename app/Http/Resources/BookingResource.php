<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Booking */
class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'hairdresser_id' => $this->hairdresser_id,
            'hairdresser_name' => $this->whenLoaded('hairdresser', fn () => $this->hairdresser?->name),
            'client_name' => $this->name,
            'email' => $this->email,
            'date' => $this->scheduled_at?->toDateString(),
            'start_time' => $this->scheduled_at?->format('H:i'),
            'scheduled_at' => $this->scheduled_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
