<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TravelDocument extends Model
{
    protected $fillable = [
        'travel_request_id',
        'uploaded_by',
        'path',
        'original_name',
    ];

    public function travelRequest()
    {
        return $this->belongsTo(TravelRequest::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
