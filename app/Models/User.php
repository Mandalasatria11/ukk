<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'department_id', 'position_id', 'role', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    public function department()
    {
        return $this->belongsTo(\App\Models\Department::class);
    }

    public function position()
    {
        return $this->belongsTo(\App\Models\Position::class);
    }

    public function travelRequests()
    {
        return $this->hasMany(\App\Models\TravelRequest::class);
    }

    public function reimbursements()
    {
        return $this->hasMany(\App\Models\Reimbursement::class);
    }

    public function approvals()
    {
        return $this->hasMany(\App\Models\Approval::class, 'approver_id');
    }

    // Role helper methods
    public function isSuperAdmin(): bool { return $this->role === 'SUPER_ADMIN'; }
    public function isAdmin(): bool { return in_array($this->role, ['ADMIN', 'SUPER_ADMIN'], true); }
    public function isManager(): bool { return $this->role === 'MANAGER'; }
    public function isHrd(): bool { return $this->role === 'HRD'; }
    public function isFinance(): bool { return $this->role === 'FINANCE'; }
    public function isEmployee(): bool { return $this->role === 'EMPLOYEE'; }

    public function canApproveTravel(): bool
    {
        return in_array($this->role, ['MANAGER', 'HRD', 'ADMIN', 'SUPER_ADMIN'], true);
    }
}
