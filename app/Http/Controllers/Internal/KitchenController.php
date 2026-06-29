<?php

namespace App\Http\Controllers\Internal;

use App\Events\KitchenStatusUpdatedEvent;
use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\KitchenQueue;
use App\Models\Order;
use App\Models\OrderCancellation;
use App\Models\MenuItem;
use App\Models\Category;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KitchenController extends Controller
{
    public function __construct(private InventoryService $inventoryService) {}

    public function index()
    {
        $queues = KitchenQueue::with(['order.items.menuItem.ingredients.inventoryItem'])
            ->whereNotIn('status', ['delivered', 'cancelled'])
            ->orderByRaw("FIELD(status, 'pending', 'preparing', 'cooking', 'ready')")
            ->orderBy('priority', 'desc')
            ->orderBy('created_at')
            ->get()
            ->groupBy('status');

        // Real-time stock for kitchen sidebar
        $stockSummary = $this->inventoryService->getKitchenStockSummary();

        // Low stock alerts (items where stock <= minimum)
        $lowStockItems = InventoryItem::whereRaw('stock <= minimum_stock')
            ->where('stock', '>', 0)
            ->select('id', 'name', 'stock', 'unit', 'minimum_stock', 'category')
            ->get();

        $emptyStockItems = InventoryItem::where('stock', '<=', 0)
            ->select('id', 'name', 'stock', 'unit', 'minimum_stock', 'category')
            ->get();

        return view('internal.kitchen', compact('queues', 'stockSummary', 'lowStockItems', 'emptyStockItems'));
    }

    public function updateStatus(Request $request, KitchenQueue $kitchenQueue)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,preparing,cooking,ready,delivered,cancelled',
            'cancellation_reason' => 'nullable|string|required_if:status,cancelled',
            'cancellation_notes'  => 'nullable|string|max:500',
        ]);

        $newStatus  = $validated['status'];
        $oldStatus  = $kitchenQueue->status;
        $order      = $kitchenQueue->order;

        DB::transaction(function () use ($kitchenQueue, $order, $validated, $newStatus, $oldStatus) {

            // ── Handle timestamps ────────────────────────────────────────────
            if ($newStatus === 'preparing' && !$kitchenQueue->started_at) {
                $kitchenQueue->started_at = now();
            }

            if ($newStatus === 'delivered') {
                $kitchenQueue->completed_at = now();
                $order->update(['status' => 'selesai', 'completed_at' => now()]);
            }

            if ($newStatus === 'ready') {
                $order->update(['status' => 'siap']);
            }

            if (in_array($newStatus, ['preparing', 'cooking'])) {
                $order->update(['status' => 'diproses']);
            }

            // ── Inventory deduction on first 'preparing' ─────────────────────
            if ($newStatus === 'preparing' && !$kitchenQueue->inventory_deducted) {
                $result = $this->inventoryService->deductForOrder($order, auth()->id());
                if (!$result['success']) {
                    // Warn but don't block — kitchen decides
                    Log::warning('Inventory insufficient for order #' . $order->order_number, $result['issues']);
                }
            }

            // ── Handle cancellation ──────────────────────────────────────────
            if ($newStatus === 'cancelled') {
                $kitchenQueue->cancellation_reason = $validated['cancellation_reason'];
                $order->update(['status' => 'dibatalkan']);

                // Record cancellation log
                OrderCancellation::create([
                    'order_id'          => $order->id,
                    'kitchen_queue_id'  => $kitchenQueue->id,
                    'reason'            => $validated['cancellation_reason'],
                    'notes'             => $validated['cancellation_notes'] ?? null,
                    'cancelled_by_role' => 'kitchen',
                    'cancelled_by'      => auth()->id(),
                    'inventory_rolled_back' => false,
                ]);

                // BUG-03 FIX: Reverse the Finance income entry with a refund expense
                \App\Models\Finance::create([
                    'type'           => 'expense',
                    'amount'         => $order->total,
                    'description'    => 'Refund Batal: ' . $order->order_number . ' (' . $validated['cancellation_reason'] . ')',
                    'category'       => 'Refund',
                    'order_id'       => $order->id,
                    'reference_type' => 'cancellation',
                    'recorded_by'    => auth()->id(),
                ]);

                // Rollback inventory if it was deducted
                if ($kitchenQueue->inventory_deducted) {
                    $this->inventoryService->rollbackForOrder($order, auth()->id());
                    $cancellation = $order->cancellation()->latest()->first();
                    $cancellation?->update(['inventory_rolled_back' => true]);
                }
            }

            $kitchenQueue->status = $newStatus;
            $kitchenQueue->save();
        });

        // Broadcast realtime status update
        try {
            $kitchenQueue->refresh();
            broadcast(new KitchenStatusUpdatedEvent($kitchenQueue))->toOthers();
        } catch (\Exception $e) {
            Log::warning('Broadcast failed: ' . $e->getMessage());
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'status'  => $newStatus,
                'message' => $this->getStatusMessage($newStatus),
            ]);
        }

        return redirect()->back()->with('success', '✅ Status dapur berhasil diperbarui!');
    }

    /**
     * AJAX: Get realtime stock data for kitchen panel.
     */
    public function stockData()
    {
        return response()->json($this->inventoryService->getKitchenStockSummary());
    }

    /**
     * Check if ingredients are sufficient for a specific order.
     */
    public function checkStock(KitchenQueue $kitchenQueue)
    {
        $order = $kitchenQueue->order->load('items.menuItem.ingredients.inventoryItem');
        $issues = [];

        foreach ($order->items as $orderItem) {
            $menuItem = $orderItem->menuItem;
            if (!$menuItem) continue;
            $stockIssues = $menuItem->checkStock($orderItem->quantity);
            foreach ($stockIssues as $issue) {
                $issues[] = $issue;
            }
        }

        return response()->json([
            'sufficient' => empty($issues),
            'issues'     => $issues,
        ]);
    }

    private function getStatusMessage(string $status): string
    {
        return match ($status) {
            'preparing' => '🔵 Pesanan mulai disiapkan',
            'cooking'   => '🟣 Pesanan sedang dimasak',
            'ready'     => '🟢 Pesanan siap diambil!',
            'delivered' => '✅ Pesanan selesai dan terkirim',
            'cancelled' => '❌ Pesanan dibatalkan',
            default     => 'Status diperbarui',
        };
    }

    // ── Kitchen Menu Management ───────────────────────────────────────────────

    public function menuList()
    {
        $categories = Category::where('is_active', true)->get();
        $menus = MenuItem::with('category')->orderBy('category_id')->get();
        return view('internal.kitchen.menus', compact('categories', 'menus'));
    }

    public function storeMenu(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|url',
        ]);

        $validated['is_available'] = true;
        
        MenuItem::create($validated);

        return redirect()->back()->with('success', 'Menu berhasil ditambahkan');
    }

    public function updateMenu(Request $request, MenuItem $menuItem)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|url',
        ]);

        $menuItem->update($validated);

        return redirect()->back()->with('success', 'Menu berhasil diperbarui');
    }

    public function toggleMenu(Request $request, MenuItem $menuItem)
    {
        $menuItem->update(['is_available' => !$menuItem->is_available]);
        return redirect()->back()->with('success', 'Status menu berhasil diubah');
    }
}
