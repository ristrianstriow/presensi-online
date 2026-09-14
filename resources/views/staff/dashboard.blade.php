<x-app-layout title="Dashboard Staff">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Dashboard Pegawai</h1>
                <p class="text-sm text-slate-500 mt-1">
                    Selamat datang, <span class="font-semibold text-slate-800">{{ $pegawai->nama ?? Auth::user()->username }}</span>. Pantau kehadiran kerja harian Anda.
                </p>
            </div>
            
            <!-- Realtime Clock widget -->
            <div x-data="{
                time: '{{ $nowInZone->format('H:i:s') }}',
                init() {
                    setInterval(() => {
                        const d = new Date();
                        this.time = d.toLocaleTimeString('id-ID', { hour12: false });
                    }, 1000);
                }
            }" class="flex items-center gap-3 bg-white px-4 py-2 rounded-xl border border-slate-200 shadow-xs">
                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <x-icon name="clock" class="w-4 h-4" />
                </div>
                <div>
                    <div class="text-[11px] text-slate-400 font-medium leading-none mb-0.5">Waktu Server ({{ $lokasi?->zona_waktu ?? 'WIB' }})</div>
                    <div class="text-sm font-bold text-slate-800 tracking-wide font-mono" x-text="time">{{ $nowInZone->format('H:i:s') }}</div>
                </div>
            </div>
        </div>
    </x-slot>

    <!-- Main Grid: Today Attendance & Office Info -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Attendance Today Hero Card -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 p-6 shadow-xs relative overflow-hidden flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-5">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-lg bg-indigo-600 text-white flex items-center justify-center shadow-xs">
                            <x-icon name="calendar" class="w-5 h-5" />
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-900 leading-tight">Presensi Hari Ini</h2>
                            <p class="text-xs text-slate-500">
                                {{ \Carbon\Carbon::parse($today)->translatedFormat('l, d F Y') }}
                            </p>
                        </div>
                    </div>

                    @if($presensiHariIni && $presensiHariIni->jam_keluar)
                        <x-badge type="success" text="Presensi Lengkap" />
                    @elseif($presensiHariIni)
                        <x-badge type="warning" text="Sudah Masuk" />
                    @else
                        <x-badge type="neutral" text="Belum Presensi" />
                    @endif
                </div>

                <!-- 2 Check-in / Check-out Status Panels -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Jam Masuk -->
                    <div class="p-4 rounded-xl border {{ $presensiHariIni ? 'border-emerald-200 bg-emerald-50/40' : 'border-slate-200 bg-slate-50/50' }}">
                        <div class="flex items-center justify-between text-xs text-slate-500 mb-2">
                            <span class="font-medium">Presensi Masuk</span>
                            <span class="text-[11px] text-slate-400">Target: {{ substr($lokasi?->jam_masuk ?? '08:00:00', 0, 5) }}</span>
                        </div>
                        @if($presensiHariIni)
                            <div class="flex items-baseline gap-2">
                                <span class="text-2xl font-extrabold text-slate-900 font-mono">
                                    {{ substr($presensiHariIni->jam_masuk, 0, 5) }}
                                </span>
                                <span class="text-xs text-slate-400 font-mono">{{ substr($presensiHariIni->jam_masuk, 5, 3) }}</span>
                            </div>
                            <div class="mt-2 flex items-center gap-2">
                                @if($statusMasuk === 'Tepat Waktu')
                                    <x-badge type="success" text="Tepat Waktu" />
                                @else
                                    <x-badge type="danger" text="Terlambat" />
                                @endif
                                <span class="text-[11px] text-slate-500">Terekam dengan GPS & Foto</span>
                            </div>
                        @else
                            <div class="text-2xl font-bold text-slate-300 font-mono">--:--</div>
                            <p class="text-xs text-slate-400 mt-2">Belum melakukan presensi masuk.</p>
                        @endif
                    </div>

                    <!-- Jam Pulang -->
                    <div class="p-4 rounded-xl border {{ ($presensiHariIni && $presensiHariIni->jam_keluar) ? 'border-emerald-200 bg-emerald-50/40' : 'border-slate-200 bg-slate-50/50' }}">
                        <div class="flex items-center justify-between text-xs text-slate-500 mb-2">
                            <span class="font-medium">Presensi Keluar</span>
                            <span class="text-[11px] text-slate-400">Mulai: {{ substr($lokasi?->jam_pulang ?? '17:00:00', 0, 5) }}</span>
                        </div>
                        @if($presensiHariIni && $presensiHariIni->jam_keluar)
                            <div class="flex items-baseline gap-2">
                                <span class="text-2xl font-extrabold text-slate-900 font-mono">
                                    {{ substr($presensiHariIni->jam_keluar, 0, 5) }}
                                </span>
                                <span class="text-xs text-slate-400 font-mono">{{ substr($presensiHariIni->jam_keluar, 5, 3) }}</span>
                            </div>
                            <div class="mt-2 flex items-center gap-2">
                                <x-badge type="success" text="Selesai Bekerja" />
                                <span class="text-[11px] text-slate-500">Terekam dengan GPS & Foto</span>
                            </div>
                        @else
                            <div class="text-2xl font-bold text-slate-300 font-mono">--:--</div>
                            <p class="text-xs text-slate-400 mt-2">
                                {{ $presensiHariIni ? 'Belum melakukan presensi keluar.' : 'Menunggu presensi masuk.' }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Action Button -->
            <div class="mt-6 pt-5 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="text-xs text-slate-500 flex items-center gap-2">
                    <x-icon name="map-pin" class="w-4 h-4 text-indigo-600 flex-shrink-0" />
                    <span>Wajib berada dalam radius lokasi kantor dan menyalakan kamera.</span>
                </div>

                @if(!$presensiHariIni)
                    <a href="{{ route('staff.presensi.index') }}" 
                       class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg shadow-xs transition-colors">
                        <x-icon name="camera" class="w-4 h-4" />
                        <span>Presensi Masuk Sekarang</span>
                    </a>
                @elseif(!$presensiHariIni->jam_keluar)
                    <a href="{{ route('staff.presensi.index') }}" 
                       class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg shadow-xs transition-colors">
                        <x-icon name="camera" class="w-4 h-4" />
                        <span>Presensi Keluar (Pulang)</span>
                    </a>
                @else
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-600 text-xs font-semibold rounded-lg">
                        <x-icon name="check-circle" class="w-4 h-4 text-emerald-600" />
                        <span>Presensi Hari Ini Selesai</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Office & Policy Info Card -->
        <div class="lg:col-span-1 bg-white rounded-xl border border-slate-200 p-6 shadow-xs flex flex-col justify-between space-y-4">
            <div>
                <div class="flex items-center gap-2 text-slate-900 font-bold text-sm mb-4">
                    <x-icon name="briefcase" class="w-4 h-4 text-indigo-600" />
                    <span>Lokasi & Jadwal Kerja</span>
                </div>

                <div class="space-y-3.5 text-xs">
                    <div>
                        <div class="text-slate-400 font-medium">Kantor Penugasan</div>
                        <div class="font-bold text-slate-800 text-sm mt-0.5">{{ $lokasi?->nama_lokasi ?? '-' }}</div>
                        <p class="text-slate-500 text-[11px] mt-0.5 leading-relaxed">{{ $lokasi?->alamat_lokasi ?? '-' }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-3 pt-3 border-t border-slate-100">
                        <div>
                            <div class="text-slate-400">Jam Masuk</div>
                            <div class="font-semibold text-slate-800 mt-0.5">{{ substr($lokasi?->jam_masuk ?? '08:00', 0, 5) }} {{ $lokasi?->zona_waktu ?? 'WIB' }}</div>
                        </div>
                        <div>
                            <div class="text-slate-400">Jam Pulang</div>
                            <div class="font-semibold text-slate-800 mt-0.5">{{ substr($lokasi?->jam_pulang ?? '17:00', 0, 5) }} {{ $lokasi?->zona_waktu ?? 'WIB' }}</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 pt-3 border-t border-slate-100">
                        <div>
                            <div class="text-slate-400">Radius Presensi</div>
                            <div class="font-semibold text-slate-800 mt-0.5">{{ $lokasi?->radius ?? 100 }} Meter</div>
                        </div>
                        <div>
                            <div class="text-slate-400">Zona Waktu</div>
                            <div class="font-semibold text-slate-800 mt-0.5">{{ $lokasi?->zona_waktu ?? 'WIB' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Link to Leave Request -->
            <div class="pt-4 border-t border-slate-100">
                <a href="{{ route('staff.ketidakhadiran.create') }}" 
                   class="w-full flex items-center justify-center gap-2 px-3 py-2 bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-semibold rounded-lg border border-slate-200 transition-colors">
                    <x-icon name="plus" class="w-4 h-4 text-indigo-600" />
                    <span>Ajukan Izin / Sakit / Cuti</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Monthly Summary Statistics -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center flex-shrink-0">
                <x-icon name="check-circle" class="w-6 h-6" />
            </div>
            <div>
                <div class="text-xs text-slate-500 font-medium">Hadir Bulan Ini</div>
                <div class="text-2xl font-bold text-slate-900 mt-0.5">{{ $totalHadirBulanIni }} <span class="text-xs text-slate-400 font-normal">Hari</span></div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0">
                <x-icon name="alert-triangle" class="w-6 h-6" />
            </div>
            <div>
                <div class="text-xs text-slate-500 font-medium">Terlambat Bulan Ini</div>
                <div class="text-2xl font-bold text-slate-900 mt-0.5">{{ $totalTerlambatBulanIni }} <span class="text-xs text-slate-400 font-normal">Hari</span></div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                <x-icon name="file-text" class="w-6 h-6" />
            </div>
            <div>
                <div class="text-xs text-slate-500 font-medium">Izin Disetujui</div>
                <div class="text-2xl font-bold text-slate-900 mt-0.5">{{ $totalIzinDisetujuiBulanIni }} <span class="text-xs text-slate-400 font-normal">Kali</span></div>
            </div>
        </div>
    </div>

    <!-- Recent Attendance Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden" x-data="{ modalOpen: false, modalImg: '', modalTitle: '' }">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <x-icon name="clock" class="w-5 h-5 text-indigo-600" />
                <h2 class="font-bold text-slate-900 text-sm">Riwayat Presensi Terbaru</h2>
            </div>
            <a href="{{ route('staff.riwayat.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                <span>Lihat Semua</span>
                <x-icon name="chevron-right" class="w-3.5 h-3.5" />
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3">Tanggal</th>
                        <th class="px-6 py-3">Jam Masuk</th>
                        <th class="px-6 py-3">Foto Masuk</th>
                        <th class="px-6 py-3">Jam Keluar</th>
                        <th class="px-6 py-3">Foto Keluar</th>
                        <th class="px-6 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($riwayatTerbaru as $item)
                        @php
                            $isLate = $lokasi && $item->jam_masuk > $lokasi->jam_masuk;
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-3.5 font-medium text-slate-900 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($item->tanggal_masuk)->translatedFormat('d M Y') }}
                            </td>
                            <td class="px-6 py-3.5 text-slate-700 font-mono text-xs whitespace-nowrap">
                                {{ substr($item->jam_masuk, 0, 5) }}
                            </td>
                            <td class="px-6 py-3.5 whitespace-nowrap">
                                @if($item->foto_masuk)
                                    <button type="button" 
                                            @click="modalOpen = true; modalImg = '{{ asset('storage/presensi/' . $item->foto_masuk) }}'; modalTitle = 'Foto Masuk: {{ $item->tanggal_masuk }}';"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium transition-colors">
                                        <x-icon name="image" class="w-3.5 h-3.5" />
                                        <span>Lihat</span>
                                    </button>
                                @else
                                    <span class="text-xs text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-3.5 text-slate-700 font-mono text-xs whitespace-nowrap">
                                {{ $item->jam_keluar ? substr($item->jam_keluar, 0, 5) : '-' }}
                            </td>
                            <td class="px-6 py-3.5 whitespace-nowrap">
                                @if($item->foto_keluar)
                                    <button type="button" 
                                            @click="modalOpen = true; modalImg = '{{ asset('storage/presensi/' . $item->foto_keluar) }}'; modalTitle = 'Foto Keluar: {{ $item->tanggal_keluar }}';"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium transition-colors">
                                        <x-icon name="image" class="w-3.5 h-3.5" />
                                        <span>Lihat</span>
                                    </button>
                                @else
                                    <span class="text-xs text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-3.5 whitespace-nowrap">
                                @if($isLate)
                                    <x-badge type="danger" text="Terlambat" />
                                @else
                                    <x-badge type="success" text="Tepat Waktu" />
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-400 text-xs">
                                Belum ada riwayat presensi tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Photo Modal -->
        <div x-show="modalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div @click.away="modalOpen = false" class="bg-white rounded-xl max-w-md w-full p-5 shadow-xl border border-slate-200">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                    <h3 class="text-sm font-bold text-slate-900" x-text="modalTitle">Bukti Foto Presensi</h3>
                    <button type="button" @click="modalOpen = false" class="text-slate-400 hover:text-slate-600">
                        <x-icon name="x" class="w-5 h-5" />
                    </button>
                </div>
                <div class="rounded-lg overflow-hidden bg-slate-100 flex items-center justify-center aspect-4/3 max-h-80">
                    <img :src="modalImg" alt="Bukti Presensi" class="w-full h-full object-cover">
                </div>
                <div class="mt-4 flex justify-end">
                    <button type="button" @click="modalOpen = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
