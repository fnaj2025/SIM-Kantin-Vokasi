<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'inventory_item_id',
        'type',
        'quantity',
        'stock_before',
        'stock_after',
        'reason',
        'reference_type',
        'reference_id',
        'performed_by',
        'notes',
    ];

    protected $casts = [
        'quantity'     => 'float',
        'stock_before' => 'float',
        'stock_after'  => 'float',
    ];

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function performedBy()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    public function getTypeIconAttribute(): string
    {
        return match ($this->type) {
            'in'         => '📥',
            'out'        => '📤',
            'adjustment' => '⚙️',
            'rollback'   => '↩️',
            default      => '📦',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'in'         => 'Masuk',
            'out'        => 'Keluar',
            'adjustment' => 'Penyesuaian',
            'rollback'   => 'Rollback',
            default      => 'Unknown',
        };
    }

    public function getReasonLabelAttribute(): string
    {
        return match ($this->reason) {
            'order_processing'  => 'Pemrosesan Order',
            'manual_restock'    => 'Restock Manual',
            'purchase'          => 'Pembelian',
            'inventory_rollback'=> 'Rollback Inventori',
            'manual_adjustment' => 'Penyesuaian Manual',
            'expired'           => 'Barang Kadaluarsa',
            default             => $this->reason ?? '-',
        };
    }
}
