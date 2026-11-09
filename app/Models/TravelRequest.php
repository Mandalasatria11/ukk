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

    public function getStageStatusLabelAttribute(): string
    {
        $approvals = $this->relationLoaded('approvals') ? $this->approvals : $this->approvals()->get();
        $lvl1 = $approvals->firstWhere('level', 1);
        $lvl2 = $approvals->firstWhere('level', 2);

        if ($this->status === 'REJECTED') {
            if ($lvl1 && $lvl1->status === 'REJECTED') {
                return 'Ditolak oleh Manager';
            }
            if ($lvl2 && $lvl2->status === 'REJECTED') {
                return 'Ditolak oleh HRD';
            }
            return 'Ditolak (Rejected)';
        }

        if ($this->status === 'APPROVED') {
            return 'Disetujui (Approved)';
        }

        if (in_array($this->status, ['PENDING_APPROVAL', 'SUBMITTED'], true)) {
            if ($lvl2 && $lvl2->status === 'PENDING') {
                return 'Disetujui Manager · Menunggu HRD';
            }
            return 'Menunggu Persetujuan Manager';
        }

        if ($this->status === 'DRAFT') {
            return 'Draft';
        }

        if ($this->status === 'CANCELLED') {
            return 'Dibatalkan';
        }

        return $this->status;
    }
}
