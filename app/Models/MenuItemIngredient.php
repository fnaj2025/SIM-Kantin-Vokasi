<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItemIngredient extends Model
{
    protected $fillable = [
        'menu_item_id',
        'inventory_item_id',
        'quantity_used',
        'unit',
    ];

    protected $casts = [
        'quantity_used' => 'float',
    ];

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }
}
