<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'date',
        'hour',
    ];

    protected $casts = [
        'date' => 'date',
        'hour' => 'datetime:H:i',
    ];
}
