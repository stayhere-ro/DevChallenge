<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'hairdresser_id',
        'name',
        'email',
        'scheduled_at',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function hairdresser(): BelongsTo
    {
        return $this->belongsTo(Hairdresser::class);
    }

    public function scopeSearch($query, ?string $term)
    {
        if ($term === null || $term === '') {
            return $query;
        }

        $like = '%'.addcslashes($term, '%_\\').'%';

        return $query->where(function ($builder) use ($like) {
            $builder->where('bookings.name', 'like', $like)
                ->orWhere('bookings.email', 'like', $like)
                ->orWhereHas('hairdresser', fn ($hairdresser) => $hairdresser->where('name', 'like', $like));
        });
    }

    public function getDateAttribute()
    {
        return $this->scheduled_at ? $this->scheduled_at->copy() : null;
    }

    public function getHourAttribute()
    {
        return $this->scheduled_at ? $this->scheduled_at->format('H:i') : null;
    }
}
