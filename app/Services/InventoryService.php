<?php

namespace App\Services;

use App\Events\InventoryUpdatedEvent;
use App\Events\PurchaseFinanceSyncEvent;
use App\Models\Finance;
use App\Models\InventoryItem;
use App\Models\KitchenQueue;
use App\Models\Order;
use App\Models\OrderCancellation;
use App\Models\Purchase;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryService
{
    /**
     * Deduct inventory based on order items' recipe/ingredients.
     * Called when kitchen starts processing (preparing/cooking).
     *
     * @return array ['success' => bool, 'issues' => array]
     */
    public function deductForOrder(Order $order, int $userId = null): array
    {
        $order->loadMissing('items.menuItem.ingredients.inventoryItem');

        $insufficientItems = [];

        // First pass: check all ingredients
        foreach ($order->items as $orderItem) {
            $menuItem = $orderItem->menuItem;
            if (!$menuItem) continue;

            $qty = $orderItem->quantity;
            foreach ($menuItem->ingredients()->with('inventoryItem')->get() as $ingredient) {
                $inv = $ingredient->inventoryItem;
                if (!$inv || $inv->trashed()) continue;

                $needed = $ingredient->quantity_used * $qty;
                if ($inv->stock < $needed) {
                    $insufficientItems[] = [
                        'item'      => $inv->name,
                        'needed'    => $needed,
                        'available' => $inv->stock,
                        'unit'      => $inv->unit,
                    ];
                }
            }
        }

        if (!empty($insufficientItems)) {
            return ['success' => false, 'issues' => $insufficientItems];
        }

        // Second pass: deduct (with row-level locking to prevent race conditions)
        DB::transaction(function () use ($order, $userId) {
            foreach ($order->items as $orderItem) {
                $menuItem = $orderItem->menuItem;
                if (!$menuItem) continue;

                $qty = $orderItem->quantity;
                foreach ($menuItem->ingredients()->with('inventoryItem')->get() as $ingredient) {
                    // BUG-02 FIX: lockForUpdate() ensures only one transaction deducts at a time
                    $inv = InventoryItem::lockForUpdate()->find($ingredient->inventory_item_id);
                    if (!$inv || $inv->trashed()) continue;

                    $needed = $ingredient->quantity_used * $qty;

                    // Re-validate after acquiring lock (double-check)
                    if ($inv->stock < $needed) {
                        throw new \Exception("Stok {$inv->name} tidak cukup saat diproses. Tersedia: {$inv->stock} {$inv->unit}, dibutuhkan: {$needed}.");
                    }

                    $inv->deductStock(
                        $needed,
                        'order_processing',
                        'order',
                        $order->id,
                        $userId
                    );

                    // Broadcast inventory update
                    broadcast(new InventoryUpdatedEvent($inv->refresh(), 'deducted'))->toOthers();
                }
            }

            // Mark queue as inventory deducted
            $order->kitchenQueue?->update(['inventory_deducted' => true]);
        });

        return ['success' => true, 'issues' => []];
    }

    /**
     * Rollback inventory deductions for a cancelled order.
     */
    public function rollbackForOrder(Order $order, int $userId = null): void
    {
        if (!$order->kitchenQueue?->inventory_deducted) {
            return; // Nothing was deducted, skip
        }

        $order->loadMissing('items.menuItem.ingredients.inventoryItem');

        DB::transaction(function () use ($order, $userId) {
            foreach ($order->items as $orderItem) {
                $menuItem = $orderItem->menuItem;
                if (!$menuItem) continue;

                $qty = $orderItem->quantity;
                foreach ($menuItem->ingredients()->with('inventoryItem')->get() as $ingredient) {
                    $inv = $ingredient->inventoryItem;
                    if (!$inv) continue;

                    $inv->addStock(
                        $ingredient->quantity_used * $qty,
                        'inventory_rollback',
                        'order',
                        $order->id,
                        $userId
                    );

                    broadcast(new InventoryUpdatedEvent($inv->refresh(), 'restocked'))->toOthers();
                }
            }

            $order->kitchenQueue?->update(['inventory_deducted' => false]);
        });
    }

    /**
     * Record a purchase and sync to Finance automatically.
     */
    public function recordPurchaseToFinance(Purchase $purchase): Finance
    {
        $totalAmount = $purchase->total_cost;

        $finance = Finance::create([
            'type'           => 'expense',
            'amount'         => $totalAmount,
            'description'    => 'Pembelian Inventori: ' . ($purchase->inventoryItem?->name ?? 'Item'),
            'category'       => 'Operational Expense',
            'purchase_id'    => $purchase->id,
            'reference_type' => 'purchase',
            'recorded_by'    => $purchase->approved_by ?? $purchase->requested_by,
        ]);

        // Broadcast finance sync
        try {
            broadcast(new PurchaseFinanceSyncEvent($purchase, $totalAmount))->toOthers();
            broadcast(new \App\Events\FinanceUpdatedEvent())->toOthers();
        } catch (\Exception $e) {
            Log::warning('Finance sync broadcast failed: ' . $e->getMessage());
        }

        return $finance;
    }

    /**
     * Record a batch purchase (multiple items) and sync to Finance.
     */
    public function recordBatchPurchaseToFinance(Purchase $purchase, array $items): Finance
    {
        $totalAmount = collect($items)->sum('total_price');

        $finance = Finance::create([
            'type'           => 'expense',
            'amount'         => $totalAmount,
            'description'    => 'Pembelian Inventori dari ' . ($purchase->supplier_name ?? 'Supplier') . ' (' . count($items) . ' item)',
            'category'       => 'Operational Expense',
            'purchase_id'    => $purchase->id,
            'reference_type' => 'purchase',
            'recorded_by'    => $purchase->approved_by ?? $purchase->requested_by,
        ]);

        try {
            broadcast(new PurchaseFinanceSyncEvent($purchase, $totalAmount))->toOthers();
            broadcast(new \App\Events\FinanceUpdatedEvent())->toOthers();
        } catch (\Exception $e) {
            Log::warning('Finance sync broadcast failed: ' . $e->getMessage());
        }

        return $finance;
    }

    /**
     * Get inventory stock status for kitchen display.
     */
    public function getKitchenStockSummary(): array
    {
        $all = InventoryItem::select('id', 'name', 'category', 'stock', 'unit', 'minimum_stock')->get();

        return [
            'total'      => $all->count(),
            'sufficient' => $all->filter(fn($i) => $i->stock_status === 'sufficient')->count(),
            'warning'    => $all->filter(fn($i) => $i->stock_status === 'warning')->count(),
            'low'        => $all->filter(fn($i) => $i->stock_status === 'low')->count(),
            'empty'      => $all->filter(fn($i) => $i->stock_status === 'empty')->count(),
            'items'      => $all->map(fn($i) => [
                'id'           => $i->id,
                'name'         => $i->name,
                'category'     => $i->category,
                'stock'        => $i->stock,
                'unit'         => $i->unit,
                'minimum_stock'=> $i->minimum_stock,
                'status'       => $i->stock_status,
                'status_label' => $i->stock_status_label,
                'status_color' => $i->stock_status_color,
            ])->values(),
        ];
    }
}
