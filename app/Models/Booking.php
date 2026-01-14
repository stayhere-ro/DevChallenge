<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'time',
        'user_id',

    ];

    protected $casts = [
        'date' => 'date',
        'time' => 'datetime:H:i',
    ];

    /**
     * Virtual accessor: $booking->date for views (derived from scheduled_at)
     */
  //  public function getDateAttribute()
   // {
  //      return $this->scheduled_at ? $this->scheduled_at->copy() : null;
  //  }

    /**
     * Virtual accessor: $booking->hour as HH:MM for views (derived from scheduled_at)
     */
  //  public function getHourAttribute()
  //  {
  //      return $this->scheduled_at ? $this->scheduled_at->format('H:i') : null;
  //  }
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function hairdresser(){
        return $this->belongsToMany(Hairdresser::class,'booking_hairdresser');
    }
}
