<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('m_user')->insert([
            [
                'user_id' => 1,
                'nama' => 'Admin',
                'username' => 'admin', // Jika tabel menggunakan 'username' bukan 'email'
                'password' => Hash::make('admin123'),
                'level_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'nama' => 'Kasir 1',
                'username' => 'kasir1',
                'password' => Hash::make('kasir123'),
                'level_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'nama' => 'Kasir 2',
                'username' => 'kasir2',
                'password' => Hash::make('kasir123'),
                'level_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
