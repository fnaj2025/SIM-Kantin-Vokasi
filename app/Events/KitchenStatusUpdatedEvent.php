<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\KitchenQueue;

class KitchenStatusUpdatedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $payload;

    public function __construct(KitchenQueue $queue)
    {
        $queue->loadMissing('order.items.menuItem');

        $this->payload = [
            'queue_id'     => $queue->id,
            'order_id'     => $queue->order_id,
            'order_number' => $queue->order?->order_number,
            'status'       => $queue->status,
            'order_status' => $queue->order?->status,
            'badge'        => $queue->status_badge,
            'cancelled_reason' => $queue->cancellation_reason,
            'updated_at'   => now()->toISOString(),
        ];
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('order.' . $this->payload['order_number']),
            new Channel('kitchen-orders'),
            new Channel('pos-updates'),
            new Channel('admin-updates'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'kitchen.status.updated';
    }
}
