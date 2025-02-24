<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StokSeeder extends Seeder
{
    public function run(): void
    {
        $data = [];
        for ($i = 1; $i <= 15; $i++) {
            $data[] = [
                'stok_id' => $i,
                'barang_id' => $i,
                'supplier_id' => ($i % 3) + 1,
                'stok_tanggal' => now(),
                'stok_jumlah' => rand(10, 100),
            ];
        }

        DB::table('t_stok')->insert($data);
    }
}
