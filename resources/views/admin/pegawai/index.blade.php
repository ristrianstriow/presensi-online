<x-app-layout title="Data Pegawai">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Data Pegawai</h1>
                <p class="text-sm text-slate-500 mt-1">Kelola master data pegawai, jabatan, lokasi presensi, dan akun pengguna.</p>
            </div>
            <a href="{{ route('admin.pegawai.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow-xs transition-colors">
                <x-icon name="plus" class="w-4 h-4" />
                <span>Tambah Pegawai Baru</span>
            </a>
        </div>
    </x-slot>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
        <form method="GET" action="{{ route('admin.pegawai.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div class="sm:col-span-2 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <x-icon name="search" class="w-4 h-4" />
                </div>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Cari nama, NRG, atau nomor HP..." 
                       class="w-full pl-9 pr-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <select name="jabatan" class="w-full py-2 px-3 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                    <option value="">Semua Jabatan</option>
                    @foreach($jabatans as $j)
                        <option value="{{ $j->jabatan }}" {{ request('jabatan') === $j->jabatan ? 'selected' : '' }}>{{ $j->jabatan }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <select name="lokasi" class="w-full py-2 px-3 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                    <option value="">Semua Lokasi</option>
                    @foreach($lokasis as $l)
                        <option value="{{ $l->nama_lokasi }}" {{ request('lokasi') === $l->nama_lokasi ? 'selected' : '' }}>{{ $l->nama_lokasi }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium rounded-lg transition-colors">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4">Pegawai</th>
                        <th class="py-3 px-4">NRG</th>
                        <th class="py-3 px-4">Jabatan</th>
                        <th class="py-3 px-4">Lokasi Presensi</th>
                        <th class="py-3 px-4">Kontak</th>
                        <th class="py-3 px-4">Akun User</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($pegawais as $pegawai)
                        <tr class="hover:bg-slate-50/75 transition-colors">
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-slate-100 border border-slate-200 overflow-hidden flex-shrink-0">
                                        @if($pegawai->foto && $pegawai->foto !== 'default.png')
                                            <img src="{{ asset('storage/pegawai/' . $pegawai->foto) }}" alt="{{ $pegawai->nama }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-slate-400">
                                                <x-icon name="user" class="w-5 h-5" />
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-semibold text-slate-900">{{ $pegawai->nama }}</div>
                                        <div class="text-xs text-slate-400">{{ $pegawai->jenis_kelamin }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4 font-mono text-xs font-semibold text-slate-800">{{ $pegawai->nrg }}</td>
                            <td class="py-3 px-4 text-slate-700">{{ $pegawai->jabatan }}</td>
                            <td class="py-3 px-4 text-slate-700">
                                <div class="inline-flex items-center gap-1.5 text-xs text-slate-600 bg-slate-100 px-2 py-1 rounded">
                                    <x-icon name="map-pin" class="w-3.5 h-3.5 text-slate-400" />
                                    <span>{{ $pegawai->lokasi_presensi }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-xs text-slate-600">
                                <div>{{ $pegawai->no_handphone }}</div>
                                <div class="text-slate-400 truncate max-w-[150px]" title="{{ $pegawai->alamat }}">{{ $pegawai->alamat }}</div>
                            </td>
                            <td class="py-3 px-4">
                                @if($pegawai->user)
                                    <div class="space-y-1">
                                        <div class="text-xs font-medium text-slate-900">{{ $pegawai->user->username }}</div>
                                        <div class="flex items-center gap-1">
                                            <x-badge :status="$pegawai->user->status" />
                                            <span class="text-[10px] uppercase font-bold tracking-wider px-1.5 py-0.5 rounded bg-slate-100 text-slate-600">
                                                {{ $pegawai->user->role }}
                                            </span>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 italic">Belum ada akun</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('admin.pegawai.show', $pegawai->id) }}" 
                                       title="Lihat Detail"
                                       class="p-1.5 text-slate-500 hover:text-indigo-600 hover:bg-slate-100 rounded transition-colors">
                                        <x-icon name="eye" class="w-4 h-4" />
                                    </a>
                                    <a href="{{ route('admin.pegawai.edit', $pegawai->id) }}" 
                                       title="Ubah Data"
                                       class="p-1.5 text-slate-500 hover:text-amber-600 hover:bg-slate-100 rounded transition-colors">
                                        <x-icon name="edit" class="w-4 h-4" />
                                    </a>
                                    <form method="POST" action="{{ route('admin.pegawai.destroy', $pegawai->id) }}" 
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus pegawai {{ $pegawai->nama }}? Akun dan data presensi terkait akan ikut terhapus.')" 
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                title="Hapus Pegawai"
                                                class="p-1.5 text-slate-500 hover:text-rose-600 hover:bg-slate-100 rounded transition-colors">
                                            <x-icon name="trash" class="w-4 h-4" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400 text-sm">
                                <x-icon name="users" class="w-8 h-8 mx-auto mb-2 text-slate-300" />
                                Tidak ada data pegawai ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pegawais->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $pegawais->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
