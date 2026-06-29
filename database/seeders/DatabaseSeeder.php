<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Default Internal Accounts
        $internals = [
            ['name' => 'Manager', 'email' => 'manager@kantinvokasi.com', 'role' => 'manager'],
            ['name' => 'Kasir Utama', 'email' => 'kasir@kantinvokasi.com', 'role' => 'kasir'],
            ['name' => 'Kepala Dapur', 'email' => 'kitchen@kantinvokasi.com', 'role' => 'kitchen'],
            ['name' => 'Finance & Accounting', 'email' => 'finance@kantinvokasi.com', 'role' => 'finance'],
            ['name' => 'Purchasing & Inventory', 'email' => 'purchasing@kantinvokasi.com', 'role' => 'purchasing'],
            ['name' => 'Admin Operasional', 'email' => 'operational@kantinvokasi.com', 'role' => 'admin_operasional'],
        ];

        foreach ($internals as $user) {
            User::create([
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => Hash::make('password123'),
                'role' => $user['role']
            ]);
        }

        // Example Customer
        $customer = User::create([
            'name' => 'Mahasiswa Teladan',
            'email' => 'customer@kantinvokasi.com',
            'password' => Hash::make('password123'),
            'role' => 'customer'
        ]);
        \App\Models\CustomerProfile::create([
            'user_id' => $customer->id,
            'nim' => '1234567890',
            'faculty' => 'Fakultas Vokasi'
        ]);

        // Categories
        $catMakanan = Category::create([
            'name' => 'Makanan Utama',
            'icon' => '🍛',
            'slug' => 'makanan-utama',
            'sort_order' => 1
        ]);

        $catMinuman = Category::create([
            'name' => 'Minuman',
            'icon' => '🥤',
            'slug' => 'minuman',
            'sort_order' => 2
        ]);

        $catCemilan = Category::create([
            'name' => 'Cemilan',
            'icon' => '🍟',
            'slug' => 'cemilan',
            'sort_order' => 3
        ]);

        // Menu Items
        MenuItem::create([
            'category_id' => $catMakanan->id,
            'name' => 'Nasi Goreng Vokasi',
            'description' => 'Nasi goreng spesial dengan telur mata sapi dan ayam suwir.',
            'price' => 15000,
            'emoji' => '🍛',
            'stock' => 50
        ]);

        MenuItem::create([
            'category_id' => $catMakanan->id,
            'name' => 'Ayam Geprek Kampus',
            'description' => 'Ayam geprek pedas nampol lengkap dengan nasi dan lalapan.',
            'price' => 18000,
            'emoji' => '🍗',
            'stock' => 30
        ]);

        MenuItem::create([
            'category_id' => $catMinuman->id,
            'name' => 'Es Teh Manis',
            'description' => 'Es teh manis segar pelepas dahaga.',
            'price' => 5000,
            'emoji' => '🍹',
            'stock' => 100
        ]);

        MenuItem::create([
            'category_id' => $catMinuman->id,
            'name' => 'Kopi Susu Gula Aren',
            'description' => 'Kopi susu dengan gula aren asli yang nikmat.',
            'price' => 12000,
            'emoji' => '☕',
            'stock' => 40
        ]);

        MenuItem::create([
            'category_id' => $catCemilan->id,
            'name' => 'Kentang Goreng',
            'description' => 'Kentang goreng renyah dengan taburan bumbu rahasia.',
            'price' => 10000,
            'emoji' => '🍟',
            'stock' => 20
        ]);
    }
}
