<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jabatan;
use App\Models\Pegawai;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JabatanController extends Controller
{
    public function index(): View
    {
        $jabatans = Jabatan::all()->map(function ($jabatan) {
            $jabatan->total_pegawai = Pegawai::where('jabatan', $jabatan->jabatan)->count();
            return $jabatan;
        });

        return view('admin.jabatan.index', compact('jabatans'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'jabatan' => ['required', 'string', 'max:50', 'unique:jabatan,jabatan'],
        ], [
            'jabatan.required' => 'Nama jabatan wajib diisi.',
            'jabatan.unique' => 'Nama jabatan sudah terdaftar.',
        ]);

        Jabatan::create([
            'jabatan' => $request->jabatan,
        ]);

        return redirect()->route('admin.jabatan.index')
            ->with('success', 'Jabatan baru berhasil ditambahkan.');
    }

    public function update(Request $request, Jabatan $jabatan): RedirectResponse
    {
        $request->validate([
            'jabatan' => ['required', 'string', 'max:50', 'unique:jabatan,jabatan,' . $jabatan->id],
        ]);

        $oldName = $jabatan->jabatan;
        $newName = $request->jabatan;

        $jabatan->update(['jabatan' => $newName]);

        // Sync pegawai records with this jabatan name
        Pegawai::where('jabatan', $oldName)->update(['jabatan' => $newName]);

        return redirect()->route('admin.jabatan.index')
            ->with('success', 'Jabatan berhasil diperbarui.');
    }

    public function destroy(Jabatan $jabatan): RedirectResponse
    {
        $count = Pegawai::where('jabatan', $jabatan->jabatan)->count();
        if ($count > 0) {
            return redirect()->route('admin.jabatan.index')
                ->with('error', "Jabatan '{$jabatan->jabatan}' tidak dapat dihapus karena masih digunakan oleh {$count} pegawai.");
        }

        $jabatan->delete();

        return redirect()->route('admin.jabatan.index')
            ->with('success', 'Jabatan berhasil dihapus.');
    }
}
