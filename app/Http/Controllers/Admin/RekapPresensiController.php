<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ketidakhadiran;
use App\Models\LokasiPresensi;
use App\Models\Pegawai;
use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RekapPresensiController extends Controller
{
    /**
     * Rekap Presensi Harian (Log Masuk & Keluar)
     */
    public function index(Request $request): View
    {
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');

        if ($bulan && $tahun) {
            $carbonMonth = Carbon::createFromDate((int) $tahun, (int) $bulan, 1);
            $startDate = $carbonMonth->copy()->startOfMonth()->toDateString();
            $endDate = $carbonMonth->copy()->endOfMonth()->toDateString();
        } else {
            $startDate = $request->input('start_date', Carbon::today()->startOfMonth()->toDateString());
            $endDate = $request->input('end_date', Carbon::today()->toDateString());
        }

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

        $daftarBulan = $this->getDaftarBulan();
        $daftarTahun = $this->getDaftarTahun();

        return view('admin.rekap.index', compact(
            'presensis',
            'pegawais',
            'lokasis',
            'startDate',
            'endDate',
            'pegawaiId',
            'lokasiName',
            'bulan',
            'tahun',
            'daftarBulan',
            'daftarTahun'
        ));
    }

    /**
     * Cetak Rekap Harian
     */
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

    /**
     * Rekap Presensi Bulanan Per Pegawai
     */
    public function bulanan(Request $request): View
    {
        $bulan = (int) $request->input('bulan', Carbon::now()->month);
        $tahun = (int) $request->input('tahun', Carbon::now()->year);
        $pegawaiId = $request->input('id_pegawai');
        $lokasiName = $request->input('lokasi');

        $carbonMonth = Carbon::createFromDate($tahun, $bulan, 1);
        $startDate = $carbonMonth->copy()->startOfMonth()->toDateString();
        $endDate = $carbonMonth->copy()->endOfMonth()->toDateString();
        $namaBulan = $this->getDaftarBulan()[$bulan] ?? 'Bulan';

        $dataRekap = $this->calculateRekapBulanan($startDate, $endDate, $pegawaiId, $lokasiName);

        $pegawais = Pegawai::orderBy('nama')->get();
        $lokasis = LokasiPresensi::orderBy('nama_lokasi')->get();
        $daftarBulan = $this->getDaftarBulan();
        $daftarTahun = $this->getDaftarTahun();

        return view('admin.rekap.bulanan', [
            'rekapPegawai' => $dataRekap['rekapPegawai'],
            'stats' => $dataRekap['stats'],
            'pegawais' => $pegawais,
            'lokasis' => $lokasis,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'namaBulan' => $namaBulan,
            'pegawaiId' => $pegawaiId,
            'lokasiName' => $lokasiName,
            'daftarBulan' => $daftarBulan,
            'daftarTahun' => $daftarTahun,
        ]);
    }

    /**
     * Cetak Laporan Rekap Presensi Bulanan Resmi
     */
    public function printBulanan(Request $request): View
    {
        $bulan = (int) $request->input('bulan', Carbon::now()->month);
        $tahun = (int) $request->input('tahun', Carbon::now()->year);
        $pegawaiId = $request->input('id_pegawai');
        $lokasiName = $request->input('lokasi');

        $carbonMonth = Carbon::createFromDate($tahun, $bulan, 1);
        $startDate = $carbonMonth->copy()->startOfMonth()->toDateString();
        $endDate = $carbonMonth->copy()->endOfMonth()->toDateString();
        $namaBulan = $this->getDaftarBulan()[$bulan] ?? 'Bulan';

        $dataRekap = $this->calculateRekapBulanan($startDate, $endDate, $pegawaiId, $lokasiName);

        return view('admin.rekap.bulanan_print', [
            'rekapPegawai' => $dataRekap['rekapPegawai'],
            'stats' => $dataRekap['stats'],
            'bulan' => $bulan,
            'tahun' => $tahun,
            'namaBulan' => $namaBulan,
            'pegawaiId' => $pegawaiId,
            'lokasiName' => $lokasiName,
        ]);
    }

    /**
     * Mengambil data rincian presensi harian per pegawai untuk modal detail
     */
    public function detailBulanan(Request $request, Pegawai $pegawai): JsonResponse
    {
        $bulan = (int) $request->input('bulan', Carbon::now()->month);
        $tahun = (int) $request->input('tahun', Carbon::now()->year);

        $carbonMonth = Carbon::createFromDate($tahun, $bulan, 1);
        $startDate = $carbonMonth->copy()->startOfMonth()->toDateString();
        $endDate = $carbonMonth->copy()->endOfMonth()->toDateString();
        $daysInMonth = $carbonMonth->daysInMonth;

        $lokasi = $pegawai->lokasiPresensiModel();
        $jamMasukKantor = $lokasi?->jam_masuk ?? '08:00:00';

        // Presensi harian
        $presensis = Presensi::where('id_pegawai', $pegawai->id)
            ->whereBetween('tanggal_masuk', [$startDate, $endDate])
            ->get()
            ->keyBy('tanggal_masuk');

        // Ketidakhadiran (disetujui)
        $ketidakhadirans = Ketidakhadiran::where('id_pegawai', $pegawai->id)
            ->where('status_pengajuan', 'disetujui')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->get()
            ->keyBy('tanggal');

        $dailyDetails = [];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::createFromDate($tahun, $bulan, $day);
            $dateStr = $date->toDateString();
            $dayOfWeek = $date->locale('id')->isoFormat('dddd');
            $isWeekend = $date->isWeekend();

            $p = $presensis->get($dateStr);
            $k = $ketidakhadirans->get($dateStr);

            if ($p) {
                $isTerlambat = ($p->jam_masuk > $jamMasukKantor);
                $dailyDetails[] = [
                    'tanggal' => $dateStr,
                    'hari' => $dayOfWeek,
                    'is_weekend' => $isWeekend,
                    'status' => 'hadir',
                    'status_label' => $isTerlambat ? 'Terlambat' : 'Tepat Waktu',
                    'status_color' => $isTerlambat ? 'amber' : 'emerald',
                    'jam_masuk' => $p->jam_masuk,
                    'jam_keluar' => $p->jam_keluar ?? '-',
                    'foto_masuk' => $p->foto_masuk ? asset('storage/presensi/' . $p->foto_masuk) : null,
                    'foto_keluar' => $p->foto_keluar ? asset('storage/presensi/' . $p->foto_keluar) : null,
                    'keterangan' => null,
                ];
            } elseif ($k) {
                $dailyDetails[] = [
                    'tanggal' => $dateStr,
                    'hari' => $dayOfWeek,
                    'is_weekend' => $isWeekend,
                    'status' => 'izin',
                    'status_label' => $k->keterangan,
                    'status_color' => strtolower($k->keterangan) === 'sakit' ? 'blue' : (strtolower($k->keterangan) === 'cuti' ? 'purple' : 'indigo'),
                    'jam_masuk' => '-',
                    'jam_keluar' => '-',
                    'foto_masuk' => null,
                    'foto_keluar' => null,
                    'keterangan' => $k->deskripsi ?? 'Pengajuan izin disetujui',
                ];
            } else {
                $dailyDetails[] = [
                    'tanggal' => $dateStr,
                    'hari' => $dayOfWeek,
                    'is_weekend' => $isWeekend,
                    'status' => $isWeekend ? 'libur' : 'alpa',
                    'status_label' => $isWeekend ? 'Libur Akhir Pekan' : 'Tidak Ada Data',
                    'status_color' => 'slate',
                    'jam_masuk' => '-',
                    'jam_keluar' => '-',
                    'foto_masuk' => null,
                    'foto_keluar' => null,
                    'keterangan' => $isWeekend ? 'Libur' : '-',
                ];
            }
        }

        return response()->json([
            'pegawai' => [
                'id' => $pegawai->id,
                'nama' => $pegawai->nama,
                'nrg' => $pegawai->nrg,
                'jabatan' => $pegawai->jabatan,
                'lokasi' => $pegawai->lokasi_presensi,
            ],
            'periode' => $this->getDaftarBulan()[$bulan] . ' ' . $tahun,
            'details' => $dailyDetails,
        ]);
    }

    /**
     * Hitung kalkulasi rekap presensi bulanan per pegawai
     */
    private function calculateRekapBulanan(string $startDate, string $endDate, ?string $pegawaiId, ?string $lokasiName): array
    {
        $query = Pegawai::with([
            'presensis' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('tanggal_masuk', [$startDate, $endDate]);
            },
            'ketidakhadirans' => function ($q) use ($startDate, $endDate) {
                $q->where('status_pengajuan', 'disetujui')
                  ->whereBetween('tanggal', [$startDate, $endDate]);
            }
        ]);

        if ($pegawaiId) {
            $query->where('id', $pegawaiId);
        }

        if ($lokasiName) {
            $query->where('lokasi_presensi', $lokasiName);
        }

        $pegawais = $query->orderBy('nama')->get();

        foreach ($pegawais as $p) {
            $lokasi = $p->lokasiPresensiModel();
            $jamMasukKantor = $lokasi?->jam_masuk ?? '08:00:00';

            $totalHadir = $p->presensis->count();
            $tepatWaktu = 0;
            $terlambat = 0;

            foreach ($p->presensis as $presensi) {
                if ($presensi->jam_masuk > $jamMasukKantor) {
                    $terlambat++;
                } else {
                    $tepatWaktu++;
                }
            }

            $sakit = 0;
            $cuti = 0;
            $izin = 0;

            foreach ($p->ketidakhadirans as $izinItem) {
                $ket = strtolower(trim($izinItem->keterangan));
                if ($ket === 'sakit') {
                    $sakit++;
                } elseif ($ket === 'cuti') {
                    $cuti++;
                } else {
                    $izin++;
                }
            }

            $totalIzinSakit = $sakit + $cuti + $izin;
            $totalAktivitas = $totalHadir + $totalIzinSakit;
            $persentaseKehadiran = $totalAktivitas > 0 ? round(($totalHadir / $totalAktivitas) * 100, 1) : 0;

            $p->total_hadir = $totalHadir;
            $p->tepat_waktu = $tepatWaktu;
            $p->terlambat = $terlambat;
            $p->sakit = $sakit;
            $p->cuti = $cuti;
            $p->izin = $izin;
            $p->total_izin_sakit = $totalIzinSakit;
            $p->persentase_kehadiran = $persentaseKehadiran;
        }

        $stats = [
            'total_pegawai' => $pegawais->count(),
            'total_hadir' => $pegawais->sum('total_hadir'),
            'total_tepat_waktu' => $pegawais->sum('tepat_waktu'),
            'total_terlambat' => $pegawais->sum('terlambat'),
            'total_sakit' => $pegawais->sum('sakit'),
            'total_cuti' => $pegawais->sum('cuti'),
            'total_izin' => $pegawais->sum('izin'),
            'total_izin_sakit' => $pegawais->sum('total_izin_sakit'),
            'rata_rata_ketepatan' => $pegawais->sum('total_hadir') > 0
                ? round(($pegawais->sum('tepat_waktu') / $pegawais->sum('total_hadir')) * 100, 1)
                : 100,
        ];

        return [
            'rekapPegawai' => $pegawais,
            'stats' => $stats,
        ];
    }

    private function getDaftarBulan(): array
    {
        return [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
    }

    private function getDaftarTahun(): array
    {
        $currentYear = (int) Carbon::now()->year;
        return range($currentYear - 2, $currentYear + 2);
    }
}
