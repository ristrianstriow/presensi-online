<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ketidakhadiran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KetidakhadiranController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->input('status', 'menunggu');

        $query = Ketidakhadiran::with('pegawai');

        if ($status !== 'semua') {
            $query->where('status_pengajuan', $status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('pegawai', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nrg', 'like', "%{$search}%");
            });
        }

        $ketidakhadirans = $query->latest('tanggal')->paginate(10)->withQueryString();

        $counts = [
            'menunggu' => Ketidakhadiran::where('status_pengajuan', 'menunggu')->count(),
            'disetujui' => Ketidakhadiran::where('status_pengajuan', 'disetujui')->count(),
            'ditolak' => Ketidakhadiran::where('status_pengajuan', 'ditolak')->count(),
            'semua' => Ketidakhadiran::count(),
        ];

        return view('admin.ketidakhadiran.index', compact('ketidakhadirans', 'status', 'counts'));
    }

    public function updateStatus(Request $request, Ketidakhadiran $ketidakhadiran): RedirectResponse
    {
        $request->validate([
            'status_pengajuan' => ['required', 'in:disetujui,ditolak,menunggu'],
        ]);

        $ketidakhadiran->update([
            'status_pengajuan' => $request->status_pengajuan,
        ]);

        $statusText = match ($request->status_pengajuan) {
            'disetujui' => 'disetujui',
            'ditolak' => 'ditolak',
            default => 'diubah menjadi menunggu',
        };

        return back()->with('success', "Pengajuan izin pegawai {$ketidakhadiran->pegawai?->nama} berhasil {$statusText}.");
    }
}
