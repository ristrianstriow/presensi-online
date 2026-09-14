<x-app-layout title="Dashboard Admin">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Dashboard Administrator</h1>
                <p class="text-sm text-slate-500 mt-1">Ringkasan statistik kehadiran pegawai dan status operasional hari ini.</p>
            </div>
            <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-medium text-slate-600 shadow-xs">
                <x-icon name="calendar" class="w-4 h-4 text-slate-400" />
                <span>{{ \Carbon\Carbon::parse($today)->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
            </div>
        </div>
    </x-slot>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Pegawai</span>
                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <x-icon name="users" class="w-4 h-4" />
                </div>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-3xl font-bold text-slate-900">{{ $totalPegawai }}</span>
                <span class="text-xs text-slate-500">Orang terdaftar</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Hadir Hari Ini</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <x-icon name="check-circle" class="w-4 h-4" />
                </div>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-3xl font-bold text-slate-900">{{ $hadirHariIniCount }}</span>
                <span class="text-xs text-slate-500">Pegawai tercatat</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Terlambat Hari Ini</span>
                <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                    <x-icon name="clock" class="w-4 h-4" />
                </div>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-3xl font-bold text-slate-900">{{ $terlambatCount }}</span>
                <span class="text-xs text-slate-500">Melebihi jam masuk</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Izin Menunggu</span>
                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                    <x-icon name="file-text" class="w-4 h-4" />
                </div>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-3xl font-bold text-slate-900">{{ $izinMenungguCount }}</span>
                <span class="text-xs text-slate-500">Perlu ditinjau</span>
            </div>
        </div>
    </div>

    <!-- Main Grid: Presensi Hari Ini & Pengajuan Izin Menunggu -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Presensi Real-Time Hari Ini -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-200 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></div>
                    <h2 class="text-base font-semibold text-slate-900">Aktivitas Presensi Hari Ini</h2>
                </div>
                <a href="{{ route('admin.rekap.index') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                    <span>Lihat Rekap Lengkap</span>
                    <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">Pegawai</th>
                            <th class="py-3 px-4">Lokasi Kantor</th>
                            <th class="py-3 px-4">Jam Masuk</th>
                            <th class="py-3 px-4">Jam Keluar</th>
                            <th class="py-3 px-4">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($presensiHariIni as $presensi)
                            @php
                                $lokasi = $presensi->pegawai?->lokasiPresensiModel();
                                $status = ($lokasi && $presensi->jam_masuk > $lokasi->jam_masuk) ? 'Terlambat' : 'Tepat Waktu';
                            @endphp
                            <tr class="hover:bg-slate-50/75 transition-colors">
                                <td class="py-3 px-4">
                                    <div class="font-medium text-slate-900">{{ $presensi->pegawai?->nama ?? '-' }}</div>
                                    <div class="text-xs text-slate-400">{{ $presensi->pegawai?->nrg }}</div>
                                </td>
                                <td class="py-3 px-4 text-slate-600">{{ $presensi->pegawai?->lokasi_presensi ?? '-' }}</td>
                                <td class="py-3 px-4">
                                    <span class="font-mono text-xs font-semibold text-slate-800">{{ $presensi->jam_masuk }}</span>
                                </td>
                                <td class="py-3 px-4">
                                    @if($presensi->jam_keluar)
                                        <span class="font-mono text-xs font-semibold text-slate-800">{{ $presensi->jam_keluar }}</span>
                                    @else
                                        <span class="text-xs text-slate-400 italic">Belum keluar</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    <x-badge :status="$status" />
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400 text-sm">
                                    <x-icon name="info" class="w-8 h-8 mx-auto mb-2 text-slate-300" />
                                    Belum ada catatan presensi masuk untuk hari ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pengajuan Izin Menunggu -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-xs flex flex-col">
            <div class="p-5 border-b border-slate-200 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <x-icon name="file-text" class="w-4 h-4 text-amber-600" />
                    <h2 class="text-base font-semibold text-slate-900">Izin Menunggu</h2>
                </div>
                <a href="{{ route('admin.ketidakhadiran.index') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                    <span>Semua</span>
                    <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                </a>
            </div>

            <div class="p-4 flex-1 space-y-3">
                @forelse($pendingIzin as $izin)
                    <div class="p-3.5 rounded-lg border border-slate-200 bg-slate-50/50 hover:bg-slate-50 transition-colors space-y-2">
                        <div class="flex items-start justify-between">
                            <div>
                                <div class="font-medium text-sm text-slate-900">{{ $izin->pegawai?->nama }}</div>
                                <div class="text-xs text-slate-500">{{ $izin->keterangan }} • {{ \Carbon\Carbon::parse($izin->tanggal)->isoFormat('D MMM Y') }}</div>
                            </div>
                            <x-badge status="menunggu" />
                        </div>
                        @if($izin->deskripsi)
                            <p class="text-xs text-slate-600 line-clamp-2">{{ $izin->deskripsi }}</p>
                        @endif
                        <div class="pt-2 flex items-center justify-end gap-2">
                            <form method="POST" action="{{ route('admin.ketidakhadiran.status', $izin->id) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status_pengajuan" value="ditolak">
                                <button type="submit" class="px-2.5 py-1 text-xs font-medium text-rose-700 bg-white border border-rose-200 hover:bg-rose-50 rounded transition-colors">
                                    Tolak
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.ketidakhadiran.status', $izin->id) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status_pengajuan" value="disetujui">
                                <button type="submit" class="px-2.5 py-1 text-xs font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded transition-colors">
                                    Setujui
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center text-slate-400 text-xs">
                        <x-icon name="check-circle" class="w-8 h-8 mx-auto mb-2 text-slate-300" />
                        Tidak ada pengajuan izin yang menunggu persetujuan.
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</x-app-layout>
