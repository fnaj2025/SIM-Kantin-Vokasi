<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'category', 'stock', 'unit',
        'minimum_stock', 'price_per_unit',
        'supplier_name', 'notes',
    ];

    protected $casts = [
        'stock'          => 'float',
        'minimum_stock'  => 'float',
        'price_per_unit' => 'float',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function ingredients()
    {
        return $this->hasMany(MenuItemIngredient::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class)->latest();
    }

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function getStockStatusAttribute(): string
    {
        if ($this->stock <= 0) return 'empty';
        if ($this->stock <= $this->minimum_stock) return 'low';
        if ($this->stock <= $this->minimum_stock * 1.5) return 'warning';
        return 'sufficient';
    }

    public function getStockStatusLabelAttribute(): string
    {
        return match ($this->stock_status) {
            'empty'     => '🚨 Habis',
            'low'       => '⚠️ Menipis',
            'warning'   => '⚡ Hampir Menipis',
            default     => '✅ Aman',
        };
    }

    public function getStockStatusColorAttribute(): string
    {
        return match ($this->stock_status) {
            'empty'   => '#EF4444',
            'low'     => '#F59E0B',
            'warning' => '#F97316',
            default   => '#10B981',
        };
    }

    /**
     * Deduct stock and record movement.
     */
    public function deductStock(float $qty, string $reason = 'order_processing', string $refType = null, int $refId = null, int $userId = null): void
    {
        $before = $this->stock;
        $this->decrement('stock', $qty);
        $this->refresh();

        StockMovement::create([
            'inventory_item_id' => $this->id,
            'type'              => 'out',
            'quantity'          => $qty,
            'stock_before'      => $before,
            'stock_after'       => $this->stock,
            'reason'            => $reason,
            'reference_type'    => $refType,
            'reference_id'      => $refId,
            'performed_by'      => $userId,
        ]);
    }

    /**
     * Add stock and record movement.
     */
    public function addStock(float $qty, string $reason = 'manual_restock', string $refType = null, int $refId = null, int $userId = null): void
    {
        $before = $this->stock;
        $this->increment('stock', $qty);
        $this->refresh();

        StockMovement::create([
            'inventory_item_id' => $this->id,
            'type'              => 'in',
            'quantity'          => $qty,
            'stock_before'      => $before,
            'stock_after'       => $this->stock,
            'reason'            => $reason,
            'reference_type'    => $refType,
            'reference_id'      => $refId,
            'performed_by'      => $userId,
        ]);
    }
}
