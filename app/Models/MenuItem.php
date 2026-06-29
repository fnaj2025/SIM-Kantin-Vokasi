<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = [
        'category_id', 'name', 'description', 'price',
        'emoji', 'image', 'is_available', 'stock', 'sort_order',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function ingredients()
    {
        return $this->hasMany(MenuItemIngredient::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Check if all inventory ingredients are sufficient for given qty.
     */
    public function checkStock(int $qty = 1): array
    {
        $issues = [];
        foreach ($this->ingredients()->with('inventoryItem')->get() as $ingredient) {
            $inv = $ingredient->inventoryItem;
            if (!$inv || $inv->trashed()) {
                $issues[] = ['item' => $ingredient->unit ?? 'bahan', 'required' => $ingredient->quantity_used * $qty, 'available' => 0];
                continue;
            }
            $needed = $ingredient->quantity_used * $qty;
            if ($inv->stock < $needed) {
                $issues[] = [
                    'item'      => $inv->name,
                    'required'  => $needed,
                    'available' => $inv->stock,
                    'unit'      => $inv->unit,
                ];
            }
        }
        return $issues;
    }

    /**
     * Update menu availability based on stock
     */
    public function autoCheckAvailability(): void
    {
        $issues = $this->checkStock(1);
        $shouldBeAvailable = empty($issues);
        
        if ($this->is_available !== $shouldBeAvailable) {
            $this->update(['is_available' => $shouldBeAvailable]);
        }
    }
}
