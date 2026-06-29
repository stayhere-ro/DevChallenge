<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'hairdresser_id',
        'scheduled_at',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    /**
     * Virtual accessor: $booking->date for views (derived from scheduled_at)
     */
    public function getDateAttribute()
    {
        return $this->scheduled_at ? $this->scheduled_at->copy() : null;
    }

    /**
     * Virtual accessor: $booking->hour as HH:MM for views (derived from scheduled_at)
     */
    public function getHourAttribute()
    {
        return $this->scheduled_at ? $this->scheduled_at->format('H:i') : null;
    }

    public function hairdresser(): BelongsTo
    {
        return $this->belongsTo(Hairdresser::class);
    }
}
