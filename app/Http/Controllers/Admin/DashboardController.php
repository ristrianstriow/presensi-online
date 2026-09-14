<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jabatan;
use App\Models\Ketidakhadiran;
use App\Models\LokasiPresensi;
use App\Models\Pegawai;
use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = Carbon::today()->toDateString();

        $totalPegawai = Pegawai::count();
        $totalLokasi = LokasiPresensi::count();
        $totalJabatan = Jabatan::count();

        $presensiHariIni = Presensi::with('pegawai')
            ->where('tanggal_masuk', $today)
            ->latest('jam_masuk')
            ->get();

        $hadirHariIniCount = $presensiHariIni->count();
        $izinMenungguCount = Ketidakhadiran::where('status_pengajuan', 'menunggu')->count();

        // Count terlambat hari ini
        $terlambatCount = 0;
        foreach ($presensiHariIni as $p) {
            $lokasi = $p->pegawai?->lokasiPresensiModel();
            if ($lokasi && $p->jam_masuk > $lokasi->jam_masuk) {
                $terlambatCount++;
            }
        }

        $pendingIzin = Ketidakhadiran::with('pegawai')
            ->where('status_pengajuan', 'menunggu')
            ->latest('tanggal')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalPegawai',
            'totalLokasi',
            'totalJabatan',
            'hadirHariIniCount',
            'terlambatCount',
            'izinMenungguCount',
            'presensiHariIni',
            'pendingIzin',
            'today'
        ));
    }
}
