<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReimbursementItem extends Model
{
    protected $fillable = [
        'reimbursement_id',
        'category', // TRANSPORT, HOTEL, MEAL, FLIGHT, OTHER
        'description',
        'amount',
        'transaction_date',
        'receipt_path',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
    ];

    public function reimbursement()
    {
        return $this->belongsTo(Reimbursement::class);
    }
}
