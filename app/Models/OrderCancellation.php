<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderCancellation extends Model
{
    protected $fillable = [
        'order_id',
        'kitchen_queue_id',
        'reason',
        'notes',
        'cancelled_by_role',
        'cancelled_by',
        'inventory_rolled_back',
    ];

    protected $casts = [
        'inventory_rolled_back' => 'boolean',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function kitchenQueue()
    {
        return $this->belongsTo(KitchenQueue::class);
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function getReasonLabelAttribute(): string
    {
        return match ($this->reason) {
            'stock_habis'         => 'Stok Habis',
            'bahan_tidak_cukup'   => 'Bahan Tidak Cukup',
            'kitchen_overload'    => 'Kitchen Overload',
            'bahan_expired'       => 'Bahan Expired',
            'pelanggan_cancel'    => 'Pelanggan Membatalkan',
            'lainnya'             => 'Lainnya',
            default               => $this->reason,
        };
    }
}
