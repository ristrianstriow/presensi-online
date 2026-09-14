<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            JabatanSeeder::class,
            LokasiPresensiSeeder::class,
            PegawaiSeeder::class,
            UserSeeder::class,
            PresensiSeeder::class,
            KetidakhadiranSeeder::class,
        ]);
    }
}
