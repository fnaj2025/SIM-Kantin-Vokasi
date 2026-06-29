<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'customer_name', 'customer_phone', 'status',
        'payment_method', 'payment_status', 'source', 'order_type',
        'subtotal', 'total', 'notes', 'processed_by', 'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function kitchenQueue()
    {
        return $this->hasOne(KitchenQueue::class);
    }

    public function cancellation()
    {
        return $this->hasOne(OrderCancellation::class);
    }

    public function finances()
    {
        return $this->hasMany(Finance::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'menunggu'   => 'Menunggu',
            'diproses'   => 'Diproses',
            'siap'       => 'Siap Diambil',
            'selesai'    => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
            default      => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'menunggu'   => '#F59E0B',
            'diproses'   => '#3B82F6',
            'siap'       => '#10B981',
            'selesai'    => '#6B7280',
            'dibatalkan' => '#EF4444',
            default      => '#94A3B8',
        };
    }
}
