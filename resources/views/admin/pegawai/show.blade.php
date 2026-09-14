<x-app-layout title="Detail Pegawai">
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.pegawai.index') }}" class="p-2 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition-colors">
                    <x-icon name="chevron-left" class="w-5 h-5" />
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $pegawai->nama }}</h1>
                    <p class="text-sm text-slate-500 mt-0.5">NRG: {{ $pegawai->nrg }} • {{ $pegawai->jabatan }}</p>
                </div>
            </div>
            <a href="{{ route('admin.pegawai.edit', $pegawai->id) }}" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow-xs transition-colors">
                <x-icon name="edit" class="w-4 h-4" />
                <span>Ubah Data</span>
            </a>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Card -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-xs p-6 space-y-6">
            <div class="flex flex-col items-center text-center">
                <div class="w-24 h-24 rounded-full overflow-hidden bg-slate-100 border-2 border-indigo-100 shadow-xs mb-3">
                    @if($pegawai->foto && $pegawai->foto !== 'default.png')
                        <img src="{{ asset('storage/pegawai/' . $pegawai->foto) }}" alt="{{ $pegawai->nama }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-slate-400">
                            <x-icon name="user" class="w-12 h-12" />
                        </div>
                    @endif
                </div>
                <h2 class="text-lg font-bold text-slate-900">{{ $pegawai->nama }}</h2>
                <p class="text-xs text-slate-500">{{ $pegawai->jabatan }}</p>

                <div class="mt-3 flex items-center gap-2">
                    @if($pegawai->user)
                        <x-badge :status="$pegawai->user->status" />
                        <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded bg-slate-100 text-slate-700">
                            {{ $pegawai->user->role }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="border-t border-slate-100 pt-4 space-y-3 text-sm">
                <div>
                    <span class="block text-xs font-semibold uppercase tracking-wider text-slate-400">NRG</span>
                    <span class="font-mono text-slate-800">{{ $pegawai->nrg }}</span>
                </div>
                <div>
                    <span class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Jenis Kelamin</span>
                    <span class="text-slate-800">{{ $pegawai->jenis_kelamin }}</span>
                </div>
                <div>
                    <span class="block text-xs font-semibold uppercase tracking-wider text-slate-400">No. Handphone / WA</span>
                    <span class="text-slate-800">{{ $pegawai->no_handphone }}</span>
                </div>
                <div>
                    <span class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Lokasi Kantor</span>
                    <span class="text-slate-800">{{ $pegawai->lokasi_presensi }}</span>
                </div>
                <div>
                    <span class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Alamat Lengkap</span>
                    <span class="text-slate-800">{{ $pegawai->alamat }}</span>
                </div>
                @if($pegawai->user)
                    <div>
                        <span class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Username Login</span>
                        <span class="font-mono text-slate-800">{{ $pegawai->user->username }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Attendance & Leave History -->
        <div class="lg:col-span-2 space-y-6">
            <!-- 10 Riwayat Presensi Terakhir -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="p-4 border-b border-slate-200 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-slate-900">Riwayat Presensi Terbaru</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                            <tr>
                                <th class="py-2.5 px-4">Tanggal</th>
                                <th class="py-2.5 px-4">Jam Masuk</th>
                                <th class="py-2.5 px-4">Jam Keluar</th>
                                <th class="py-2.5 px-4">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse($pegawai->presensis as $presensi)
                                @php
                                    $status = ($lokasi && $presensi->jam_masuk > $lokasi->jam_masuk) ? 'Terlambat' : 'Tepat Waktu';
                                @endphp
                                <tr class="hover:bg-slate-50/75">
                                    <td class="py-2.5 px-4 text-xs font-medium text-slate-900">{{ \Carbon\Carbon::parse($presensi->tanggal_masuk)->isoFormat('D MMM Y') }}</td>
                                    <td class="py-2.5 px-4 font-mono text-xs text-slate-700">{{ $presensi->jam_masuk }}</td>
                                    <td class="py-2.5 px-4 font-mono text-xs text-slate-700">{{ $presensi->jam_keluar ?? '-' }}</td>
                                    <td class="py-2.5 px-4">
                                        <x-badge :status="$status" />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-6 text-center text-xs text-slate-400">Belum ada riwayat presensi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 10 Riwayat Izin Terakhir -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="p-4 border-b border-slate-200 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-slate-900">Riwayat Pengajuan Izin</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                            <tr>
                                <th class="py-2.5 px-4">Tanggal</th>
                                <th class="py-2.5 px-4">Keterangan</th>
                                <th class="py-2.5 px-4">Deskripsi</th>
                                <th class="py-2.5 px-4">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse($pegawai->ketidakhadirans as $izin)
                                <tr class="hover:bg-slate-50/75">
                                    <td class="py-2.5 px-4 text-xs font-medium text-slate-900">{{ \Carbon\Carbon::parse($izin->tanggal)->isoFormat('D MMM Y') }}</td>
                                    <td class="py-2.5 px-4 text-xs text-slate-700">{{ $izin->keterangan }}</td>
                                    <td class="py-2.5 px-4 text-xs text-slate-500 truncate max-w-xs">{{ $izin->deskripsi ?? '-' }}</td>
                                    <td class="py-2.5 px-4">
                                        <x-badge :status="$izin->status_pengajuan" />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-6 text-center text-xs text-slate-400">Belum ada pengajuan izin.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
