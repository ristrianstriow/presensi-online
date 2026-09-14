<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Ketidakhadiran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class KetidakhadiranController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        $ketidakhadirans = Ketidakhadiran::where('id_pegawai', $pegawai?->id)
            ->latest('tanggal')
            ->paginate(10);

        return view('staff.ketidakhadiran.index', compact('ketidakhadirans'));
    }

    public function create(): View
    {
        return view('staff.ketidakhadiran.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'keterangan' => ['required', 'string', 'max:50'],
            'tanggal' => ['required', 'date'],
            'deskripsi' => ['nullable', 'string', 'max:225'],
            'file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:3072'],
        ], [
            'keterangan.required' => 'Jenis keterangan izin wajib dipilih.',
            'tanggal.required' => 'Tanggal izin wajib diisi.',
            'file.max' => 'Ukuran file lampiran maksimal 3MB.',
        ]);

        $user = Auth::user();
        $pegawai = $user->pegawai;

        if (!$pegawai) {
            return back()->with('error', 'Data pegawai tidak ditemukan.');
        }

        $fileName = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('ketidakhadiran', $fileName, 'public');
        }

        Ketidakhadiran::create([
            'id_pegawai' => $pegawai->id,
            'keterangan' => $request->keterangan,
            'tanggal' => $request->tanggal,
            'deskripsi' => $request->deskripsi,
            'file' => $fileName,
            'status_pengajuan' => 'menunggu',
        ]);

        return redirect()->route('staff.ketidakhadiran.index')
            ->with('success', 'Pengajuan izin berhasil dikirimkan dan sedang menunggu persetujuan administrator.');
    }
}
