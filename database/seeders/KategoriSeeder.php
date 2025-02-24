<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['kategori_id' => 1, 'kategori_kode' => 'ELEC', 'kategori_nama' => 'Elektronik'],
            ['kategori_id' => 2, 'kategori_kode' => 'FASH', 'kategori_nama' => 'Fashion'],
            ['kategori_id' => 3, 'kategori_kode' => 'FOOD', 'kategori_nama' => 'Makanan'],
            ['kategori_id' => 4, 'kategori_kode' => 'TOYS', 'kategori_nama' => 'Mainan'],
            ['kategori_id' => 5, 'kategori_kode' => 'HOME', 'kategori_nama' => 'Peralatan Rumah'],
        ];
        DB::table('m_kategori')->insert($data);
    }
}
