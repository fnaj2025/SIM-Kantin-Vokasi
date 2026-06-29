<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Pastikan tabel suppliers punya kolom yang cukup ───────────────
        if (Schema::hasTable('suppliers')) {
            Schema::table('suppliers', function (Blueprint $table) {
                if (! Schema::hasColumn('suppliers', 'phone')) {
                    $table->string('phone')->nullable()->after('name');
                }
                if (! Schema::hasColumn('suppliers', 'address')) {
                    $table->text('address')->nullable()->after('phone');
                }
                if (! Schema::hasColumn('suppliers', 'email')) {
                    $table->string('email')->nullable()->after('address');
                }
            });
        }

        // ── Buat supplier default agar FK constraint tidak pernah gagal ──
        // Ini hanya dijalankan jika tabel suppliers kosong
        if (Schema::hasTable('suppliers') && DB::table('suppliers')->count() === 0) {
            DB::table('suppliers')->insert([
                'name'       => 'Supplier Umum',
                'phone'      => null,
                'address'    => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // ── Buat supplier_id nullable di purchases ────────────────────────
        // Ini mencegah FK crash jika supplier belum ada
        if (Schema::hasTable('purchases')) {
            // Cek apakah kolom sudah nullable atau belum
            $columns = DB::select("SHOW COLUMNS FROM purchases WHERE Field = 'supplier_id'");
            if (!empty($columns) && $columns[0]->Null === 'NO') {
                // Drop FK dulu, ubah ke nullable, buat FK baru dengan nullOnDelete
                try {
                    Schema::table('purchases', function (Blueprint $table) {
                        $table->dropForeign(['supplier_id']);
                    });
                } catch (\Exception $e) {
                    // FK mungkin punya nama berbeda, abaikan
                }

                Schema::table('purchases', function (Blueprint $table) {
                    $table->foreignId('supplier_id')
                        ->nullable()
                        ->change();

                    $table->foreign('supplier_id')
                        ->references('id')
                        ->on('suppliers')
                        ->nullOnDelete();
                });
            }
        }
    }

    public function down(): void
    {
        // Tidak perlu rollback agresif untuk migration safety ini
    }
};