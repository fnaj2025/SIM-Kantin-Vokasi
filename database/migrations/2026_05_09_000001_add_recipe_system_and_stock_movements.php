<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Recipe Ingredients: MenuItem ↔ InventoryItem mapping ──────────────
        Schema::create('menu_item_ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('inventory_item_id')->constrained()->onDelete('cascade');
            $table->decimal('quantity_used', 10, 3)->default(1); // per 1 portion
            $table->string('unit')->nullable(); // override unit
            $table->timestamps();
            $table->unique(['menu_item_id', 'inventory_item_id']);
        });

        // ── Stock Movement Log ────────────────────────────────────────────────
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['in', 'out', 'adjustment', 'rollback']);
            $table->decimal('quantity', 10, 3);
            $table->decimal('stock_before', 10, 3);
            $table->decimal('stock_after', 10, 3);
            $table->string('reason')->nullable(); // 'order_processing', 'manual_restock', etc.
            $table->string('reference_type')->nullable(); // 'order', 'purchase', 'manual'
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['inventory_item_id', 'created_at']);
            $table->index(['reference_type', 'reference_id']);
        });

        // ── Cancellation Logs for Kitchen Orders ─────────────────────────────
        Schema::create('order_cancellations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('kitchen_queue_id')->nullable()->constrained()->nullOnDelete();
            $table->string('reason');
            $table->text('notes')->nullable();
            $table->enum('cancelled_by_role', ['kitchen', 'admin', 'system', 'customer'])->default('kitchen');
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('inventory_rolled_back')->default(false);
            $table->timestamps();
        });

        // ── Purchase items detail ─────────────────────────────────────────────
        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained()->onDelete('cascade');
            $table->foreignId('inventory_item_id')->constrained()->onDelete('cascade');
            $table->decimal('quantity', 10, 3);
            $table->decimal('unit_price', 12, 2);
            $table->decimal('total_price', 12, 2);
            $table->timestamps();
        });

        // ── Add cancellation fields to kitchen_queues ─────────────────────────
        Schema::table('kitchen_queues', function (Blueprint $table) {
            $table->enum('status', ['pending','preparing','cooking','ready','delivered','cancelled'])
                  ->default('pending')
                  ->change();
            $table->string('cancellation_reason')->nullable()->after('completed_at');
            $table->boolean('inventory_deducted')->default(false)->after('cancellation_reason');
        });

        // ── Add purchase_id reference to finances ─────────────────────────────
        Schema::table('finances', function (Blueprint $table) {
            $table->foreignId('purchase_id')->nullable()->constrained()->nullOnDelete()->after('order_id');
            $table->string('reference_type')->nullable()->after('purchase_id'); // 'order','purchase','manual'
        });

        // ── Add supplier_id + notes to purchases ──────────────────────────────
        Schema::table('purchases', function (Blueprint $table) {
            $table->text('notes')->nullable()->after('approved_by');
            $table->string('supplier_name')->nullable()->after('notes'); // quick supplier name
        });

        // ── Add soft-delete to inventory_items ────────────────────────────────
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->softDeletes()->after('updated_at');
            $table->string('supplier_name')->nullable()->after('price_per_unit');
            $table->text('notes')->nullable()->after('supplier_name');
        });
    }

    public function down(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn(['supplier_name', 'notes']);
        });
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn(['notes', 'supplier_name']);
        });
        Schema::table('finances', function (Blueprint $table) {
            $table->dropForeign(['purchase_id']);
            $table->dropColumn(['purchase_id', 'reference_type']);
        });
        Schema::table('kitchen_queues', function (Blueprint $table) {
            $table->dropColumn(['cancellation_reason', 'inventory_deducted']);
        });
        Schema::dropIfExists('purchase_items');
        Schema::dropIfExists('order_cancellations');
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('menu_item_ingredients');
    }
};
