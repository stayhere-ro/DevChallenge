<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    public function isHairdresser(): bool
    {
        return $this->role === 'hairdresser';
    }

    /**
     * Client history: bookings matched by email
     */
    public function bookingsAsClient(): HasMany
    {
        return $this->hasMany(Booking::class, 'email', 'email');
    }

    /**
     * Hairdresser view: bookings matched by hairdresser_id
     */
    public function bookingsAsHairdresser(): HasMany
    {
        return $this->hasMany(Booking::class, 'hairdresser_id');
    }
}
