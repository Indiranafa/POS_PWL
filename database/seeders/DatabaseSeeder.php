<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
                LevelSeeder::class,
                KategoriSeeder::class,
                SupplierSeeder::class,
                UserSeeder::class, // Pastikan ini sebelum PenjualanSeeder
                BarangSeeder::class,
                StokSeeder::class,
                PenjualanSeeder::class,
            
        ]);
    }
}
