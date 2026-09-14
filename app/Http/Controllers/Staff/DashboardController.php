<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Ketidakhadiran;
use App\Models\Presensi;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;
        $lokasi = $pegawai?->lokasiPresensiModel();

        $nowInZone = AttendanceService::getNowInTimezone($lokasi?->zona_waktu ?? 'WIB');
        $today = $nowInZone->toDateString();

        $presensiHariIni = null;
        $statusMasuk = null;
        if ($pegawai) {
            $presensiHariIni = Presensi::where('id_pegawai', $pegawai->id)
                ->where('tanggal_masuk', $today)
                ->first();

            if ($presensiHariIni && $lokasi) {
                $statusMasuk = AttendanceService::checkInStatus($presensiHariIni->jam_masuk, $lokasi->jam_masuk);
            }
        }

        // Monthly statistics
        $startOfMonth = $nowInZone->copy()->startOfMonth()->toDateString();
        $endOfMonth = $nowInZone->copy()->endOfMonth()->toDateString();

        $presensisBulanIni = $pegawai ? Presensi::where('id_pegawai', $pegawai->id)
            ->whereBetween('tanggal_masuk', [$startOfMonth, $endOfMonth])
            ->get() : collect();

        $totalHadirBulanIni = $presensisBulanIni->count();
        $totalTerlambatBulanIni = 0;
        foreach ($presensisBulanIni as $p) {
            if ($lokasi && $p->jam_masuk > $lokasi->jam_masuk) {
                $totalTerlambatBulanIni++;
            }
        }

        $totalIzinDisetujuiBulanIni = $pegawai ? Ketidakhadiran::where('id_pegawai', $pegawai->id)
            ->where('status_pengajuan', 'disetujui')
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->count() : 0;

        $riwayatTerbaru = $pegawai ? Presensi::where('id_pegawai', $pegawai->id)
            ->latest('tanggal_masuk')
            ->take(5)
            ->get() : collect();

        return view('staff.dashboard', compact(
            'pegawai',
            'lokasi',
            'presensiHariIni',
            'statusMasuk',
            'today',
            'nowInZone',
            'totalHadirBulanIni',
            'totalTerlambatBulanIni',
            'totalIzinDisetujuiBulanIni',
            'riwayatTerbaru'
        ));
    }
}
