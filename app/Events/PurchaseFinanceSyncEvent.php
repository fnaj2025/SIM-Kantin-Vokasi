<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Purchase;

class PurchaseFinanceSyncEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $payload;

    public function __construct(Purchase $purchase, float $totalAmount)
    {
        $this->payload = [
            'purchase_id'  => $purchase->id,
            'total_amount' => $totalAmount,
            'description'  => 'Pembelian Inventori - ' . ($purchase->supplier_name ?? 'Supplier'),
            'updated_at'   => now()->toISOString(),
        ];
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('admin-updates'),
            new Channel('finance-updates'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'purchase.finance.synced';
    }
}
