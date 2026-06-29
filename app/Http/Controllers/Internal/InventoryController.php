<?php

namespace App\Http\Controllers\Internal;

use App\Events\InventoryUpdatedEvent;
use App\Http\Controllers\Controller;
use App\Models\Finance;
use App\Models\InventoryItem;
use App\Models\Purchase;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryController extends Controller
{
    public function __construct(private InventoryService $inventoryService) {}

    public function index()
    {
        $items      = InventoryItem::orderBy('category')->orderBy('name')->get()->groupBy('category');
        $allItems   = InventoryItem::orderBy('name')->get();
        $lowStock   = InventoryItem::whereRaw('stock <= minimum_stock')->where('stock', '>', 0)->get();
        $emptyStock = InventoryItem::where('stock', '<=', 0)->get();
        $suppliers  = Supplier::orderBy('name')->get();

        $stats = [
            'total'    => InventoryItem::count(),
            'low'      => $lowStock->count(),
            'critical' => InventoryItem::where('stock', '<=', 0)->count(),
            'value'    => InventoryItem::selectRaw('SUM(stock * price_per_unit) as total')->value('total') ?? 0,
        ];

        $movements = StockMovement::with(['inventoryItem', 'performedBy'])
            ->latest()->take(30)->get();

        $purchases = Purchase::with(['inventoryItem', 'requestedBy', 'approvedBy'])
            ->latest()->take(20)->get();

        return view('internal.inventory', compact(
            'items', 'allItems', 'lowStock', 'emptyStock',
            'stats', 'movements', 'purchases', 'suppliers'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'category'       => 'required|string',
            'stock'          => 'required|numeric|min:0',
            'unit'           => 'required|string',
            'minimum_stock'  => 'required|numeric|min:0',
            'price_per_unit' => 'required|numeric|min:0',
            'supplier_name'  => 'nullable|string|max:255',
            'notes'          => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($validated) {
            $item = InventoryItem::create($validated);

            if ($item->stock > 0) {
                StockMovement::create([
                    'inventory_item_id' => $item->id,
                    'type'              => 'in',
                    'quantity'          => $item->stock,
                    'stock_before'      => 0,
                    'stock_after'       => $item->stock,
                    'reason'            => 'initial_stock',
                    'performed_by'      => auth()->id(),
                ]);
            }

            try {
                broadcast(new InventoryUpdatedEvent($item, 'added'))->toOthers();
            } catch (\Exception $e) {
                Log::warning('Inventory broadcast failed: ' . $e->getMessage());
            }
        });

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Item berhasil ditambahkan!']);
        }

        return redirect()->route('internal.inventory.index')->with('success', 'Item inventori berhasil ditambahkan!');
    }

    public function update(Request $request, InventoryItem $inventory)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'category'       => 'required|string',
            'stock'          => 'required|numeric|min:0',
            'unit'           => 'required|string',
            'minimum_stock'  => 'required|numeric|min:0',
            'price_per_unit' => 'required|numeric|min:0',
            'supplier_name'  => 'nullable|string|max:255',
            'notes'          => 'nullable|string|max:500',
            'reason'         => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($inventory, $validated) {
            $oldStock = $inventory->stock;
            $newStock = (float) $validated['stock'];

            $inventory->update($validated);

            if ($oldStock != $newStock) {
                $diff = $newStock - $oldStock;
                StockMovement::create([
                    'inventory_item_id' => $inventory->id,
                    'type'              => $diff > 0 ? 'in' : 'out',
                    'quantity'          => abs($diff),
                    'stock_before'      => $oldStock,
                    'stock_after'       => $newStock,
                    'reason'            => $validated['reason'] ?? 'manual_adjustment',
                    'performed_by'      => auth()->id(),
                ]);
            }

            try {
                broadcast(new InventoryUpdatedEvent($inventory->refresh(), 'updated'))->toOthers();
            } catch (\Exception $e) {
                Log::warning('Inventory broadcast failed: ' . $e->getMessage());
            }
        });

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Item berhasil diperbarui!']);
        }

        return redirect()->route('internal.inventory.index')->with('success', 'Item berhasil diperbarui!');
    }

    public function restock(Request $request, InventoryItem $inventory)
    {
        $validated = $request->validate([
            'quantity' => 'required|numeric|min:0.1',
            'reason'   => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($inventory, $validated) {
            $inventory->addStock(
                (float) $validated['quantity'],
                $validated['reason'] ?? 'manual_restock',
                null, null,
                auth()->id()
            );

            try {
                broadcast(new InventoryUpdatedEvent($inventory->refresh(), 'restocked'))->toOthers();
            } catch (\Exception $e) {
                Log::warning('Inventory broadcast failed: ' . $e->getMessage());
            }
        });

        if ($request->wantsJson()) {
            return response()->json([
                'success'   => true,
                'new_stock' => $inventory->stock,
                'message'   => 'Stok berhasil ditambahkan!',
            ]);
        }

        return redirect()->route('internal.inventory.index')->with('success', 'Stok berhasil ditambahkan!');
    }

    public function destroy(InventoryItem $inventory)
    {
        $inventory->delete();

        try {
            broadcast(new InventoryUpdatedEvent($inventory, 'deleted'))->toOthers();
        } catch (\Exception $e) {
            Log::warning('Inventory broadcast failed: ' . $e->getMessage());
        }

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Item berhasil dihapus!']);
        }

        return redirect()->route('internal.inventory.index')->with('success', 'Item berhasil dihapus!');
    }

    /**
     * Catat pembelian bahan & auto-sync ke Finance.
     *
     * FIX: Daripada hardcode supplier_id=1 (yang bisa tidak ada),
     * sekarang auto-create supplier dari supplier_name jika belum ada,
     * sehingga FK constraint tidak pernah gagal.
     */
    public function storePurchase(Request $request)
    {
        $validated = $request->validate([
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'supplier_name'     => 'required|string|max:255',
            'quantity'          => 'required|numeric|min:0.1',
            'unit_price'        => 'required|numeric|min:0',
            'notes'             => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($validated) {
            $item      = InventoryItem::findOrFail($validated['inventory_item_id']);
            $totalCost = (float) $validated['quantity'] * (float) $validated['unit_price'];

            // ── FIX: firstOrCreate supplier dari nama yang diinput ────────
            // Ini mencegah FK violation saat tabel suppliers kosong.
            $supplier = Supplier::firstOrCreate(
                ['name' => $validated['supplier_name']],
                [
                    'name'    => $validated['supplier_name'],
                    'phone'   => null,
                    'address' => null,
                ]
            );

            $purchase = Purchase::create([
                'supplier_id'       => $supplier->id,          // ← selalu valid
                'inventory_item_id' => $validated['inventory_item_id'],
                'quantity'          => $validated['quantity'],
                'total_cost'        => $totalCost,
                'status'            => 'received',
                'requested_by'      => auth()->id(),
                'approved_by'       => auth()->id(),
                'notes'             => $validated['notes'] ?? null,
                'supplier_name'     => $validated['supplier_name'],
            ]);

            // Tambah stok
            $item->addStock(
                (float) $validated['quantity'],
                'purchase',
                'purchase',
                $purchase->id,
                auth()->id()
            );

            // Catat ke Finance sebagai pengeluaran
            $this->inventoryService->recordPurchaseToFinance($purchase);

            try {
                broadcast(new InventoryUpdatedEvent($item->refresh(), 'restocked'))->toOthers();
            } catch (\Exception $e) {
                Log::warning('Inventory broadcast failed: ' . $e->getMessage());
            }
        });

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pembelian berhasil dicatat dan masuk ke keuangan!',
            ]);
        }

        return redirect()->route('internal.inventory.index')
            ->with('success', 'Pembelian dicatat dan finance diperbarui!');
    }

    public function stockApi()
    {
        return response()->json($this->inventoryService->getKitchenStockSummary());
    }

    public function movements(InventoryItem $inventory)
    {
        $movements = $inventory->stockMovements()->with('performedBy')->paginate(20);
        return response()->json($movements);
    }
}