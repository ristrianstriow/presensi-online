<?php

namespace Database\Seeders;

use App\Models\Pegawai;
use Illuminate\Database\Seeder;

class PegawaiSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'id' => 1,
                'nrg' => 'NRG001',
                'nama' => 'Budi Santoso',
                'jenis_kelamin' => 'Laki-laki',
                'alamat' => 'Jl. Kenanga No. 1, Depok',
                'no_handphone' => '081234567890',
                'jabatan' => 'Administrator',
                'lokasi_presensi' => 'SMK Tunas Media',
                'foto' => 'default.png',
            ],
            [
                'id' => 2,
                'nrg' => 'NRG002',
                'nama' => 'Siti Aminah',
                'jenis_kelamin' => 'Perempuan',
                'alamat' => 'Jl. Mawar No. 2, Depok',
                'no_handphone' => '081234567891',
                'jabatan' => 'Staff Operasional',
                'lokasi_presensi' => 'SMK Tunas Media',
                'foto' => 'default.png',
            ],
            [
                'id' => 3,
                'nrg' => 'NRG003',
                'nama' => 'Andi Wijaya',
                'jenis_kelamin' => 'Laki-laki',
                'alamat' => 'Jl. Melati No. 3, Jakarta',
                'no_handphone' => '081234567892',
                'jabatan' => 'Staff Keuangan',
                'lokasi_presensi' => 'Kantor Cabang',
                'foto' => 'default.png',
            ],
        ];

        foreach ($data as $item) {
            Pegawai::updateOrCreate(['id' => $item['id']], $item);
        }
    }
}
