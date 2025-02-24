<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['supplier_id' => 1, 'supplier_kode' => 'SUP001', 'supplier_nama' => 'PT Sumber Elektronik', 'supplier_alamat' => 'Jl. Elektronik No. 1'],
            ['supplier_id' => 2, 'supplier_kode' => 'SUP002', 'supplier_nama' => 'CV Fashion Trend', 'supplier_alamat' => 'Jl. Mode No. 2'],
            ['supplier_id' => 3, 'supplier_kode' => 'SUP003', 'supplier_nama' => 'UD Makanan Sehat', 'supplier_alamat' => 'Jl. Kuliner No. 3'],
        ];
        DB::table('m_supplier')->insert($data);
    }
}
