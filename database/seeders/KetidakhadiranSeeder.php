<?php

namespace Database\Seeders;

use App\Models\Ketidakhadiran;
use Illuminate\Database\Seeder;

class KetidakhadiranSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'id' => 1,
                'id_pegawai' => 2,
                'keterangan' => 'Sakit',
                'tanggal' => '2026-09-05',
                'deskripsi' => 'Demam tinggi, disertai surat dokter',
                'file' => 'surat_dokter_siti.pdf',
                'status_pengajuan' => 'disetujui',
            ],
            [
                'id' => 2,
                'id_pegawai' => 3,
                'keterangan' => 'Cuti',
                'tanggal' => '2026-09-10',
                'deskripsi' => 'Cuti tahunan keperluan keluarga',
                'file' => null,
                'status_pengajuan' => 'menunggu',
            ],
        ];

        foreach ($data as $item) {
            Ketidakhadiran::updateOrCreate(['id' => $item['id']], $item);
        }
    }
}
