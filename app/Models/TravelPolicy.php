<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TravelPolicy extends Model
{
    protected $fillable = [
        'name',
        'position_id',
        'destination_tier',
        'max_budget',
        'is_active',
    ];

    public function position()
    {
        return $this->belongsTo(Position::class);
    }
}
