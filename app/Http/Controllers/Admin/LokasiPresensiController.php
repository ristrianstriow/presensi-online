<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LokasiPresensi;
use App\Models\Pegawai;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LokasiPresensiController extends Controller
{
    public function index(): View
    {
        $lokasis = LokasiPresensi::all()->map(function ($lokasi) {
            $lokasi->total_pegawai = Pegawai::where('lokasi_presensi', $lokasi->nama_lokasi)->count();
            return $lokasi;
        });

        return view('admin.lokasi.index', compact('lokasis'));
    }

    public function create(): View
    {
        return view('admin.lokasi.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nama_lokasi' => ['required', 'string', 'max:50', 'unique:lokasi_presensi,nama_lokasi'],
            'alamat_lokasi' => ['required', 'string', 'max:225'],
            'tipe_lokasi' => ['required', 'string', 'max:50'],
            'latitude' => ['required', 'string', 'max:50'],
            'longitude' => ['required', 'string', 'max:50'],
            'radius' => ['required', 'integer', 'min:5'],
            'zona_waktu' => ['required', 'in:WIB,WITA,WIT'],
            'jam_masuk' => ['required', 'date_format:H:i'],
            'jam_pulang' => ['required', 'date_format:H:i'],
        ], [
            'nama_lokasi.unique' => 'Nama lokasi sudah terdaftar.',
            'radius.min' => 'Radius minimal adalah 5 meter.',
        ]);

        LokasiPresensi::create([
            'nama_lokasi' => $request->nama_lokasi,
            'alamat_lokasi' => $request->alamat_lokasi,
            'tipe_lokasi' => $request->tipe_lokasi,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'radius' => $request->radius,
            'zona_waktu' => $request->zona_waktu,
            'jam_masuk' => $request->jam_masuk,
            'jam_pulang' => $request->jam_pulang,
        ]);

        return redirect()->route('admin.lokasi.index')
            ->with('success', 'Lokasi presensi baru berhasil ditambahkan.');
    }

    public function edit(LokasiPresensi $lokasi): View
    {
        return view('admin.lokasi.edit', compact('lokasi'));
    }

    public function update(Request $request, LokasiPresensi $lokasi): RedirectResponse
    {
        $request->validate([
            'nama_lokasi' => ['required', 'string', 'max:50', 'unique:lokasi_presensi,nama_lokasi,' . $lokasi->id],
            'alamat_lokasi' => ['required', 'string', 'max:225'],
            'tipe_lokasi' => ['required', 'string', 'max:50'],
            'latitude' => ['required', 'string', 'max:50'],
            'longitude' => ['required', 'string', 'max:50'],
            'radius' => ['required', 'integer', 'min:5'],
            'zona_waktu' => ['required', 'in:WIB,WITA,WIT'],
            'jam_masuk' => ['required'],
            'jam_pulang' => ['required'],
        ]);

        $oldName = $lokasi->nama_lokasi;
        $newName = $request->nama_lokasi;

        $lokasi->update([
            'nama_lokasi' => $newName,
            'alamat_lokasi' => $request->alamat_lokasi,
            'tipe_lokasi' => $request->tipe_lokasi,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'radius' => $request->radius,
            'zona_waktu' => $request->zona_waktu,
            'jam_masuk' => $request->jam_masuk,
            'jam_pulang' => $request->jam_pulang,
        ]);

        if ($oldName !== $newName) {
            Pegawai::where('lokasi_presensi', $oldName)->update(['lokasi_presensi' => $newName]);
        }

        return redirect()->route('admin.lokasi.index')
            ->with('success', 'Lokasi presensi berhasil diperbarui.');
    }

    public function destroy(LokasiPresensi $lokasi): RedirectResponse
    {
        $count = Pegawai::where('lokasi_presensi', $lokasi->nama_lokasi)->count();
        if ($count > 0) {
            return redirect()->route('admin.lokasi.index')
                ->with('error', "Lokasi '{$lokasi->nama_lokasi}' tidak dapat dihapus karena masih digunakan oleh {$count} pegawai.");
        }

        $lokasi->delete();

        return redirect()->route('admin.lokasi.index')
            ->with('success', 'Lokasi presensi berhasil dihapus.');
    }
}
