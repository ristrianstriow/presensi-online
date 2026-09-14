<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        return view('admin.profile', compact('user', 'pegawai'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        $request->validate([
            'nama' => ['required', 'string', 'max:50'],
            'no_handphone' => ['required', 'string', 'max:20'],
            'alamat' => ['required', 'string', 'max:225'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'current_password' => ['nullable', 'required_with:new_password', 'current_password'],
            'new_password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ], [
            'current_password.current_password' => 'Password saat ini tidak sesuai.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        if ($pegawai) {
            $fotoName = $pegawai->foto;
            if ($request->hasFile('foto')) {
                if ($fotoName && $fotoName !== 'default.png') {
                    Storage::disk('public')->delete('pegawai/' . $fotoName);
                }
                $file = $request->file('foto');
                $fotoName = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('pegawai', $fotoName, 'public');
            }

            $pegawai->update([
                'nama' => $request->nama,
                'no_handphone' => $request->no_handphone,
                'alamat' => $request->alamat,
                'foto' => $fotoName,
            ]);
        }

        if ($request->filled('new_password')) {
            $user->update([
                'password' => Hash::make($request->new_password),
            ]);
        }

        return back()->with('success', 'Profil admin berhasil diperbarui.');
    }
}
