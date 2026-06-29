<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'icon', 'slug', 'is_active', 'sort_order'];

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }
}
