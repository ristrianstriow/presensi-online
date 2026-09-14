<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RiwayatPresensiController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;
        $lokasi = $pegawai?->lokasiPresensiModel();

        $bulan = $request->input('bulan', Carbon::now()->format('m'));
        $tahun = $request->input('tahun', Carbon::now()->format('Y'));

        $query = Presensi::where('id_pegawai', $pegawai?->id)
            ->whereMonth('tanggal_masuk', $bulan)
            ->whereYear('tanggal_masuk', $tahun);

        $presensis = $query->orderBy('tanggal_masuk', 'desc')->paginate(15)->withQueryString();

        foreach ($presensis as $p) {
            $p->status_masuk = ($lokasi && $p->jam_masuk > $lokasi->jam_masuk) ? 'Terlambat' : 'Tepat Waktu';
        }

        return view('staff.riwayat.index', compact('presensis', 'bulan', 'tahun', 'lokasi'));
    }
}
