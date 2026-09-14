<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LokasiPresensi;
use App\Models\Pegawai;
use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RekapPresensiController extends Controller
{
    public function index(Request $request): View
    {
        $startDate = $request->input('start_date', Carbon::today()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());
        $pegawaiId = $request->input('id_pegawai');
        $lokasiName = $request->input('lokasi');

        $query = Presensi::with('pegawai')
            ->whereBetween('tanggal_masuk', [$startDate, $endDate]);

        if ($pegawaiId) {
            $query->where('id_pegawai', $pegawaiId);
        }

        if ($lokasiName) {
            $query->whereHas('pegawai', function ($q) use ($lokasiName) {
                $q->where('lokasi_presensi', $lokasiName);
            });
        }

        $presensis = $query->latest('tanggal_masuk')
            ->latest('jam_masuk')
            ->paginate(15)
            ->withQueryString();

        // Attach calculated status (Tepat Waktu / Terlambat)
        foreach ($presensis as $p) {
            $lokasi = $p->pegawai?->lokasiPresensiModel();
            $p->status_masuk = ($lokasi && $p->jam_masuk > $lokasi->jam_masuk) ? 'Terlambat' : 'Tepat Waktu';
        }

        $pegawais = Pegawai::orderBy('nama')->get();
        $lokasis = LokasiPresensi::orderBy('nama_lokasi')->get();

        return view('admin.rekap.index', compact(
            'presensis',
            'pegawais',
            'lokasis',
            'startDate',
            'endDate',
            'pegawaiId',
            'lokasiName'
        ));
    }

    public function print(Request $request): View
    {
        $startDate = $request->input('start_date', Carbon::today()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());
        $pegawaiId = $request->input('id_pegawai');
        $lokasiName = $request->input('lokasi');

        $query = Presensi::with('pegawai')
            ->whereBetween('tanggal_masuk', [$startDate, $endDate]);

        if ($pegawaiId) {
            $query->where('id_pegawai', $pegawaiId);
        }

        if ($lokasiName) {
            $query->whereHas('pegawai', function ($q) use ($lokasiName) {
                $q->where('lokasi_presensi', $lokasiName);
            });
        }

        $presensis = $query->orderBy('tanggal_masuk', 'asc')
            ->orderBy('jam_masuk', 'asc')
            ->get();

        foreach ($presensis as $p) {
            $lokasi = $p->pegawai?->lokasiPresensiModel();
            $p->status_masuk = ($lokasi && $p->jam_masuk > $lokasi->jam_masuk) ? 'Terlambat' : 'Tepat Waktu';
        }

        return view('admin.rekap.print', compact(
            'presensis',
            'startDate',
            'endDate',
            'pegawaiId',
            'lokasiName'
        ));
    }
}
