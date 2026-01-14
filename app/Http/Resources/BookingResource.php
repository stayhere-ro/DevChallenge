<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'hairdressers' => $this->hairdresser->map(function ($hairdresser) {
                return [
                        'hairdresser_id' => $hairdresser->pivot->hairdresser_id,
                        'booking_id' => $hairdresser->pivot->booking_id,
                ];
            }),
        ];
    }
}
