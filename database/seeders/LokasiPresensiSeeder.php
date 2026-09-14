<?php

namespace Database\Seeders;

use App\Models\LokasiPresensi;
use Illuminate\Database\Seeder;

class LokasiPresensiSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'id' => 1,
                'nama_lokasi' => 'SMK Tunas Media',
                'alamat_lokasi' => 'Jl. Raya Cinangka No. 88, Kedaung, Kec. Sawangan, Kota Depok, Jawa Barat',
                'tipe_lokasi' => 'Sekolah',
                'latitude' => '-6.370835',
                'longitude' => '106.746056',
                'radius' => 100,
                'zona_waktu' => 'WIB',
                'jam_masuk' => '07:00:00',
                'jam_pulang' => '15:30:00',
            ],
            [
                'id' => 2,
                'nama_lokasi' => 'Kantor Cabang',
                'alamat_lokasi' => 'Jl. Sudirman No. 5, Jakarta',
                'tipe_lokasi' => 'Kantor',
                'latitude' => '-6.2088',
                'longitude' => '106.8456',
                'radius' => 150,
                'zona_waktu' => 'WIB',
                'jam_masuk' => '08:30:00',
                'jam_pulang' => '17:30:00',
            ],
        ];

        foreach ($data as $item) {
            LokasiPresensi::updateOrCreate(['id' => $item['id']], $item);
        }
    }
}
