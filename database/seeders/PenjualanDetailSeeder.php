<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenjualanDetailSeeder extends Seeder
{
    public function run(): void
    {
        $data = [];
        for ($i = 1; $i <= 10; $i++) {
            for ($j = 1; $j <= 3; $j++) {
                $barangId = (($i - 1) * 3 + $j) % 15 + 1;
                $harga = DB::table('m_barang')->where('barang_id', $barangId)->value('harga_jual');

                $data[] = [
                    'penjualan_detail_id' => (($i - 1) * 3 + $j),
                    'penjualan_id' => $i,
                    'barang_id' => $barangId,
                    'harga' => $harga,
                    'jumlah' => rand(1, 5),
                ];
            }
        }

        DB::table('t_penjualan_detail')->insert($data);
    }
}
