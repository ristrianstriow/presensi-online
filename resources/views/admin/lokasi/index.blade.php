<x-app-layout title="Lokasi Presensi">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Lokasi Presensi</h1>
                <p class="text-sm text-slate-500 mt-1">Kelola titik koordinat GPS kantor, batas radius kehadiran, dan jam kerja pegawai.</p>
            </div>
            <a href="{{ route('admin.lokasi.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow-xs transition-colors">
                <x-icon name="plus" class="w-4 h-4" />
                <span>Tambah Lokasi Baru</span>
            </a>
        </div>
    </x-slot>

    <!-- Table Card -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4">Nama & Alamat Lokasi</th>
                        <th class="py-3 px-4">Tipe</th>
                        <th class="py-3 px-4">Koordinat GPS</th>
                        <th class="py-3 px-4">Radius</th>
                        <th class="py-3 px-4">Jam Kerja</th>
                        <th class="py-3 px-4">Pegawai</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($lokasis as $lokasi)
                        <tr class="hover:bg-slate-50/75 transition-colors">
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900">{{ $lokasi->nama_lokasi }}</div>
                                <div class="text-xs text-slate-500 truncate max-w-xs">{{ $lokasi->alamat_lokasi }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-700">
                                    {{ $lokasi->tipe_lokasi }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-mono text-xs text-slate-700">
                                <div>Lat: {{ $lokasi->latitude }}</div>
                                <div>Lng: {{ $lokasi->longitude }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-700 bg-indigo-50 px-2 py-1 rounded">
                                    <x-icon name="map-pin" class="w-3.5 h-3.5" />
                                    <span>{{ $lokasi->radius }} Meter</span>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-xs">
                                <div class="font-mono text-slate-800 font-semibold">{{ substr($lokasi->jam_masuk, 0, 5) }} - {{ substr($lokasi->jam_pulang, 0, 5) }}</div>
                                <div class="text-slate-400 uppercase font-semibold text-[10px]">{{ $lokasi->zona_waktu }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                                    <x-icon name="users" class="w-3.5 h-3.5 text-slate-400" />
                                    {{ $lokasi->total_pegawai }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('admin.lokasi.edit', $lokasi->id) }}" 
                                       title="Ubah Lokasi"
                                       class="p-1.5 text-slate-500 hover:text-indigo-600 hover:bg-slate-100 rounded transition-colors">
                                        <x-icon name="edit" class="w-4 h-4" />
                                    </a>
                                    <form method="POST" action="{{ route('admin.lokasi.destroy', $lokasi->id) }}" 
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus lokasi {{ $lokasi->nama_lokasi }}?')"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                title="Hapus Lokasi"
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
                                <x-icon name="map-pin" class="w-8 h-8 mx-auto mb-2 text-slate-300" />
                                Belum ada lokasi presensi terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
