<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KitchenQueue extends Model
{
    protected $fillable = [
        'order_id', 'status', 'priority',
        'estimated_completion', 'started_at', 'completed_at',
        'cancellation_reason', 'inventory_deducted',
    ];

    protected $casts = [
        'estimated_completion' => 'datetime',
        'started_at'           => 'datetime',
        'completed_at'         => 'datetime',
        'inventory_deducted'   => 'boolean',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function cancellation()
    {
        return $this->hasOne(OrderCancellation::class);
    }

    public function getElapsedMinutesAttribute(): int
    {
        if (!$this->started_at) return 0;
        return (int) $this->started_at->diffInMinutes(now());
    }

    public function getRemainingMinutesAttribute(): int
    {
        if (!$this->estimated_completion) return 0;
        $remaining = (int) now()->diffInMinutes($this->estimated_completion, false);
        return max($remaining, 0);
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'pending'   => ['label' => 'Menunggu',  'color' => '#F59E0B', 'bg' => '#FEF3C7'],
            'preparing' => ['label' => 'Disiapkan', 'color' => '#3B82F6', 'bg' => '#DBEAFE'],
            'cooking'   => ['label' => 'Dimasak',   'color' => '#8B5CF6', 'bg' => '#EDE9FE'],
            'ready'     => ['label' => 'Siap',      'color' => '#10B981', 'bg' => '#D1FAE5'],
            'delivered' => ['label' => 'Selesai',   'color' => '#6B7280', 'bg' => '#F3F4F6'],
            'cancelled' => ['label' => 'Dibatalkan','color' => '#EF4444', 'bg' => '#FEE2E2'],
            default     => ['label' => 'Unknown',   'color' => '#6B7280', 'bg' => '#F3F4F6'],
        };
    }
}
