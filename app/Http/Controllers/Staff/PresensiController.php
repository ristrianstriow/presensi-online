<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use App\Services\AttendanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PresensiController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;
        $lokasi = $pegawai?->lokasiPresensiModel();

        $nowInZone = AttendanceService::getNowInTimezone($lokasi?->zona_waktu ?? 'WIB');
        $today = $nowInZone->toDateString();

        $presensiHariIni = null;
        $sudahMasuk = false;
        $sudahKeluar = false;

        if ($pegawai) {
            $presensiHariIni = Presensi::where('id_pegawai', $pegawai->id)
                ->where('tanggal_masuk', $today)
                ->first();

            $sudahMasuk = $presensiHariIni !== null;
            $sudahKeluar = $presensiHariIni && $presensiHariIni->jam_keluar !== null;
        }

        return view('staff.presensi.index', compact(
            'pegawai',
            'lokasi',
            'presensiHariIni',
            'sudahMasuk',
            'sudahKeluar',
            'today',
            'nowInZone'
        ));
    }

    public function validateLocation(Request $request): JsonResponse
    {
        $request->validate([
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
        ]);

        $user = Auth::user();
        $pegawai = $user->pegawai;
        $lokasi = $pegawai?->lokasiPresensiModel();

        if (!$lokasi) {
            return response()->json([
                'success' => false,
                'message' => 'Data lokasi presensi pegawai tidak ditemukan.',
            ], 404);
        }

        $distance = AttendanceService::calculateDistance(
            (float) $request->latitude,
            (float) $request->longitude,
            (float) $lokasi->latitude,
            (float) $lokasi->longitude
        );

        $inRadius = $distance <= $lokasi->radius;

        return response()->json([
            'success' => true,
            'in_radius' => $inRadius,
            'distance' => $distance,
            'max_radius' => $lokasi->radius,
            'office_name' => $lokasi->nama_lokasi,
            'office_latitude' => (float) $lokasi->latitude,
            'office_longitude' => (float) $lokasi->longitude,
            'message' => $inRadius
                ? "Anda berada di dalam radius presensi ({$distance} meter dari kantor)."
                : "Anda berada di luar radius presensi ({$distance} meter dari kantor, radius maksimal {$lokasi->radius} meter).",
        ]);
    }

    public function submit(Request $request): JsonResponse
    {
        $request->validate([
            'tipe' => ['required', 'in:masuk,keluar'],
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'foto' => ['required', 'string'], // base64 data url
        ]);

        $user = Auth::user();
        $pegawai = $user->pegawai;

        if (!$pegawai) {
            return response()->json([
                'success' => false,
                'message' => 'Data pegawai tidak ditemukan.',
            ], 404);
        }

        $lokasi = $pegawai->lokasiPresensiModel();
        if (!$lokasi) {
            return response()->json([
                'success' => false,
                'message' => 'Lokasi presensi belum diatur untuk pegawai ini.',
            ], 422);
        }

        // Server-side validation of GPS radius
        $distance = AttendanceService::calculateDistance(
            (float) $request->latitude,
            (float) $request->longitude,
            (float) $lokasi->latitude,
            (float) $lokasi->longitude
        );

        if ($distance > $lokasi->radius) {
            return response()->json([
                'success' => false,
                'message' => "Presensi ditolak. Anda berada di luar radius presensi ({$distance} meter dari {$lokasi->nama_lokasi}, maksimal {$lokasi->radius} meter).",
            ], 422);
        }

        // Process Base64 photo
        $fotoData = $request->foto;
        if (preg_match('/^data:image\/(\w+);base64,/', $fotoData, $type)) {
            $fotoData = substr($fotoData, strpos($fotoData, ',') + 1);
            $fotoType = strtolower($type[1]); // jpg, png, jpeg
            $fotoData = base64_decode($fotoData);

            if ($fotoData === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format foto kamera tidak valid.',
                ], 422);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Data foto kamera tidak valid.',
            ], 422);
        }

        $nowInZone = AttendanceService::getNowInTimezone($lokasi->zona_waktu);
        $today = $nowInZone->toDateString();
        $currentTime = $nowInZone->toTimeString();

        $tipe = $request->tipe;
        $filename = "{$tipe}_{$pegawai->nrg}_" . $nowInZone->format('Ymd_His') . ".jpg";
        Storage::disk('public')->put("presensi/{$filename}", $fotoData);

        if ($tipe === 'masuk') {
            $existing = Presensi::where('id_pegawai', $pegawai->id)
                ->where('tanggal_masuk', $today)
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah melakukan presensi masuk hari ini.',
                ], 422);
            }

            $presensi = Presensi::create([
                'id_pegawai' => $pegawai->id,
                'tanggal_masuk' => $today,
                'jam_masuk' => $currentTime,
                'foto_masuk' => $filename,
            ]);

            $status = AttendanceService::checkInStatus($currentTime, $lokasi->jam_masuk);

            return response()->json([
                'success' => true,
                'message' => "Presensi masuk berhasil dicatat pada pukul {$currentTime} ({$status}).",
                'status' => $status,
                'jam' => $currentTime,
            ]);
        } else {
            $presensi = Presensi::where('id_pegawai', $pegawai->id)
                ->where('tanggal_masuk', $today)
                ->first();

            if (!$presensi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda belum melakukan presensi masuk hari ini.',
                ], 422);
            }

            if ($presensi->jam_keluar !== null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah melakukan presensi keluar hari ini.',
                ], 422);
            }

            $presensi->update([
                'tanggal_keluar' => $today,
                'jam_keluar' => $currentTime,
                'foto_keluar' => $filename,
            ]);

            return response()->json([
                'success' => true,
                'message' => "Presensi keluar berhasil dicatat pada pukul {$currentTime}.",
                'jam' => $currentTime,
            ]);
        }
    }
}
