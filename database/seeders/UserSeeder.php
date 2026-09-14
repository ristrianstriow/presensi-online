<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'id' => 1,
                'id_pegawai' => 1,
                'username' => 'admin',
                'password' => Hash::make('password123'),
                'status' => 'aktif',
                'role' => 'admin',
            ],
            [
                'id' => 2,
                'id_pegawai' => 2,
                'username' => 'siti.aminah',
                'password' => Hash::make('password123'),
                'status' => 'aktif',
                'role' => 'staff',
            ],
            [
                'id' => 3,
                'id_pegawai' => 3,
                'username' => 'andi.wijaya',
                'password' => Hash::make('password123'),
                'status' => 'aktif',
                'role' => 'staff',
            ],
        ];

        foreach ($data as $item) {
            User::updateOrCreate(['id' => $item['id']], $item);
        }
    }
}
