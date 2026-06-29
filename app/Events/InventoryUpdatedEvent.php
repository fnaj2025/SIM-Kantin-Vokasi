<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\InventoryItem;

class InventoryUpdatedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $payload;

    public function __construct(InventoryItem $item, string $action = 'updated')
    {
        $this->payload = [
            'item_id'      => $item->id,
            'name'         => $item->name,
            'stock'        => $item->stock,
            'unit'         => $item->unit,
            'status'       => $item->stock_status,
            'status_label' => $item->stock_status_label,
            'status_color' => $item->stock_status_color,
            'minimum_stock'=> $item->minimum_stock,
            'action'       => $action, // 'deducted', 'restocked', 'added', 'deleted'
            'updated_at'   => now()->toISOString(),
        ];
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('inventory-updates'),
            new Channel('kitchen-orders'), // Kitchen needs to see live stock
        ];
    }

    public function broadcastAs(): string
    {
        return 'inventory.updated';
    }
}
