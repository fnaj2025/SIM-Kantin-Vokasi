<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique(); // ORD-001
            $table->string('customer_name');
            $table->string('customer_phone')->nullable();
            $table->enum('status', ['menunggu', 'diproses', 'siap', 'selesai', 'dibatalkan'])->default('menunggu');
            $table->enum('payment_method', ['tunai', 'qris'])->default('tunai');
            $table->enum('payment_status', ['belum_bayar', 'sudah_bayar'])->default('belum_bayar');
            $table->enum('source', ['pos', 'online'])->default('online');
            $table->enum('order_type', ['dine_in', 'takeaway'])->default('dine_in');
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
