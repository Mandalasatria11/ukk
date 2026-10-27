<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reimbursement extends Model
{
    protected $fillable = [
        'travel_request_id',
        'user_id',
        'advance_amount',
        'approved_amount',
        'difference_amount',
        'status', // DRAFT, PENDING_VERIFICATION, APPROVED, REJECTED, PENDING_PAYMENT, PAID
        'external_journal_ref',
        'finance_note',
    ];

    protected $casts = [
        'advance_amount' => 'decimal:2',
        'approved_amount' => 'decimal:2',
        'difference_amount' => 'decimal:2',
    ];

    public function travelRequest()
    {
        return $this->belongsTo(TravelRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(ReimbursementItem::class);
    }

    public function getTotalItemsAmountAttribute()
    {
        return $this->items->sum('amount');
    }
}
