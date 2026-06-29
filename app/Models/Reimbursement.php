<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reimbursement extends Model
{
    protected $fillable = [
        'user_id', 'amount', 'description', 'category',
        'status', 'receipt_path', 'approved_by', 'approved_at', 'notes'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getStatusBadgeAttribute(): array
    {
        return match($this->status) {
            'pending'  => ['label' => 'Menunggu', 'color' => '#F59E0B', 'bg' => '#FEF3C7'],
            'approved' => ['label' => 'Disetujui', 'color' => '#10B981', 'bg' => '#D1FAE5'],
            'rejected' => ['label' => 'Ditolak',   'color' => '#EF4444', 'bg' => '#FEE2E2'],
            default    => ['label' => 'Unknown',   'color' => '#6B7280', 'bg' => '#F3F4F6'],
        };
    }
}
