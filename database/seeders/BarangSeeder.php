<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangSeeder extends Seeder
{
    public function run(): void
    {
        $data = [];
        $barangList = [
            ['TV LED', 'ELEC', 2500000, 3000000],
            ['Kulkas', 'ELEC', 3500000, 4200000],
            ['Laptop', 'ELEC', 8000000, 9500000],
            ['Mesin Cuci', 'ELEC', 4000000, 4700000],
            ['Setrika', 'ELEC', 250000, 350000],

            ['Baju Batik', 'FASH', 150000, 200000],
            ['Celana Jeans', 'FASH', 200000, 250000],
            ['Sepatu Sneakers', 'FASH', 350000, 450000],
            ['Jaket Kulit', 'FASH', 500000, 600000],
            ['Topi Baseball', 'FASH', 100000, 150000],

            ['Roti Tawar', 'FOOD', 15000, 20000],
            ['Susu UHT', 'FOOD', 25000, 30000],
            ['Kopi Bubuk', 'FOOD', 50000, 60000],
            ['Coklat Batang', 'FOOD', 20000, 25000],
            ['Minyak Goreng', 'FOOD', 40000, 45000],
        ];

        foreach ($barangList as $index => $barang) {
            $data[] = [
                'barang_id' => $index + 1,
                'kategori_id' => ($index % 3) + 1,
                'supplier_id' => (($index % 3) + 1),
                'barang_kode' => 'BRG' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'barang_nama' => $barang[0],
                'harga_beli' => $barang[2],
                'harga_jual' => $barang[3],
            ];
        }

        DB::table('m_barang')->insert($data);
    }
}
