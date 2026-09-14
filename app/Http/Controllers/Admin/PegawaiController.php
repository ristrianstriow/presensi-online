<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jabatan;
use App\Models\LokasiPresensi;
use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PegawaiController extends Controller
{
    public function index(Request $request): View
    {
        $query = Pegawai::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nrg', 'like', "%{$search}%")
                  ->orWhere('no_handphone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('jabatan')) {
            $query->where('jabatan', $request->jabatan);
        }

        if ($request->filled('lokasi')) {
            $query->where('lokasi_presensi', $request->lokasi);
        }

        $pegawais = $query->latest('id')->paginate(10)->withQueryString();
        $jabatans = Jabatan::all();
        $lokasis = LokasiPresensi::all();

        return view('admin.pegawai.index', compact('pegawais', 'jabatans', 'lokasis'));
    }

    public function create(): View
    {
        $jabatans = Jabatan::all();
        $lokasis = LokasiPresensi::all();

        return view('admin.pegawai.create', compact('jabatans', 'lokasis'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nrg' => ['required', 'string', 'max:50', 'unique:pegawai,nrg'],
            'nama' => ['required', 'string', 'max:50'],
            'jenis_kelamin' => ['required', 'in:Laki-laki,Perempuan'],
            'alamat' => ['required', 'string', 'max:225'],
            'no_handphone' => ['required', 'string', 'max:20'],
            'jabatan' => ['required', 'string', 'max:50'],
            'lokasi_presensi' => ['required', 'string', 'max:50'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'in:admin,staff'],
        ], [
            'nrg.unique' => 'NRG sudah terdaftar.',
            'username.unique' => 'Username sudah digunakan.',
        ]);

        $fotoName = 'default.png';
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $fotoName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('pegawai', $fotoName, 'public');
        }

        $pegawai = Pegawai::create([
            'nrg' => $request->nrg,
            'nama' => $request->nama,
            'jenis_kelamin' => $request->jenis_kelamin,
            'alamat' => $request->alamat,
            'no_handphone' => $request->no_handphone,
            'jabatan' => $request->jabatan,
            'lokasi_presensi' => $request->lokasi_presensi,
            'foto' => $fotoName,
        ]);

        User::create([
            'id_pegawai' => $pegawai->id,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'status' => 'aktif',
            'role' => $request->role,
        ]);

        return redirect()->route('admin.pegawai.index')
            ->with('success', 'Data pegawai ' . $pegawai->nama . ' beserta akun user berhasil ditambahkan.');
    }

    public function show(Pegawai $pegawai): View
    {
        $pegawai->load(['user', 'presensis' => function ($q) {
            $q->latest('tanggal_masuk')->take(10);
        }, 'ketidakhadirans' => function ($q) {
            $q->latest('tanggal')->take(10);
        }]);

        $lokasi = $pegawai->lokasiPresensiModel();

        return view('admin.pegawai.show', compact('pegawai', 'lokasi'));
    }

    public function edit(Pegawai $pegawai): View
    {
        $jabatans = Jabatan::all();
        $lokasis = LokasiPresensi::all();
        $user = $pegawai->user;

        return view('admin.pegawai.edit', compact('pegawai', 'jabatans', 'lokasis', 'user'));
    }

    public function update(Request $request, Pegawai $pegawai): RedirectResponse
    {
        $user = $pegawai->user;

        $request->validate([
            'nrg' => ['required', 'string', 'max:50', 'unique:pegawai,nrg,' . $pegawai->id],
            'nama' => ['required', 'string', 'max:50'],
            'jenis_kelamin' => ['required', 'in:Laki-laki,Perempuan'],
            'alamat' => ['required', 'string', 'max:225'],
            'no_handphone' => ['required', 'string', 'max:20'],
            'jabatan' => ['required', 'string', 'max:50'],
            'lokasi_presensi' => ['required', 'string', 'max:50'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username,' . ($user ? $user->id : '')],
            'password' => ['nullable', 'string', 'min:6'],
            'role' => ['required', 'in:admin,staff'],
            'status' => ['required', 'in:aktif,nonaktif'],
        ]);

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
            'nrg' => $request->nrg,
            'nama' => $request->nama,
            'jenis_kelamin' => $request->jenis_kelamin,
            'alamat' => $request->alamat,
            'no_handphone' => $request->no_handphone,
            'jabatan' => $request->jabatan,
            'lokasi_presensi' => $request->lokasi_presensi,
            'foto' => $fotoName,
        ]);

        if ($user) {
            $userData = [
                'username' => $request->username,
                'role' => $request->role,
                'status' => $request->status,
            ];
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }
            $user->update($userData);
        }

        return redirect()->route('admin.pegawai.index')
            ->with('success', 'Data pegawai ' . $pegawai->nama . ' berhasil diperbarui.');
    }

    public function destroy(Pegawai $pegawai): RedirectResponse
    {
        $nama = $pegawai->nama;

        if ($pegawai->foto && $pegawai->foto !== 'default.png') {
            Storage::disk('public')->delete('pegawai/' . $pegawai->foto);
        }

        $pegawai->delete();

        return redirect()->route('admin.pegawai.index')
            ->with('success', 'Data pegawai ' . $nama . ' berhasil dihapus.');
    }
}
