<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TravelBooking extends Model
{
    protected $fillable = [
        'travel_request_id',
        'provider',
        'booking_type', // FLIGHT, HOTEL, TRAIN, OTHER
        'reference',
        'cost',
        'status', // PENDING, CONFIRMED, CANCELLED
    ];

    protected $casts = [
        'cost' => 'decimal:2',
    ];

    public function travelRequest()
    {
        return $this->belongsTo(TravelRequest::class);
    }
}
