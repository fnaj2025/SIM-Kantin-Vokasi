<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = [
        'supplier_id', 'inventory_item_id', 'quantity',
        'total_cost', 'status',
        'requested_by', 'approved_by',
        'notes', 'supplier_name',
    ];

    protected $casts = [
        'quantity'   => 'float',
        'total_cost' => 'float',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function finance()
    {
        return $this->hasOne(Finance::class, 'purchase_id');
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'requested' => ['label' => 'Diminta',    'bg' => '#DBEAFE', 'color' => '#1E40AF'],
            'approved'  => ['label' => 'Disetujui',  'bg' => '#D1FAE5', 'color' => '#065F46'],
            'received'  => ['label' => 'Diterima',   'bg' => '#EDE9FE', 'color' => '#5B21B6'],
            'rejected'  => ['label' => 'Ditolak',    'bg' => '#FEE2E2', 'color' => '#991B1B'],
            default     => ['label' => 'Unknown',    'bg' => '#F1F5F9', 'color' => '#475569'],
        };
    }
}
