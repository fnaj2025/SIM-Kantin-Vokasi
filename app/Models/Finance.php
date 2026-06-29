<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Finance extends Model
{
    protected $fillable = [
        'type', 'amount', 'description', 'category',
        'order_id', 'purchase_id', 'reference_type',
        'recorded_by',
    ];

    protected $casts = [
        'amount' => 'float',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
