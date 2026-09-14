<?php

namespace Database\Seeders;

use App\Models\Jabatan;
use Illuminate\Database\Seeder;

class JabatanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['id' => 1, 'jabatan' => 'Administrator'],
            ['id' => 2, 'jabatan' => 'Staff Operasional'],
            ['id' => 3, 'jabatan' => 'Staff Keuangan'],
        ];

        foreach ($data as $item) {
            Jabatan::updateOrCreate(['id' => $item['id']], $item);
        }
    }
}
