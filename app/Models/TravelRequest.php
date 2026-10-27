<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TravelRequest extends Model
{
    protected $fillable = [
        'user_id',
        'department_id',
        'destination',
        'purpose',
        'start_date',
        'end_date',
        'estimated_cost',
        'policy_id',
        'status', // DRAFT, PENDING_APPROVAL, APPROVED, REJECTED, CANCELLED, COMPLETED
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'estimated_cost' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function policy()
    {
        return $this->belongsTo(TravelPolicy::class, 'policy_id');
    }

    public function approvals()
    {
        return $this->hasMany(Approval::class)->orderBy('level');
    }

    public function documents()
    {
        return $this->hasMany(TravelDocument::class);
    }

    public function bookings()
    {
        return $this->hasMany(TravelBooking::class);
    }

    public function reimbursement()
    {
        return $this->hasOne(Reimbursement::class);
    }
}
