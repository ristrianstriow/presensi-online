<x-app-layout title="Riwayat Presensi">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Riwayat Presensi Pribadi</h1>
                <p class="text-sm text-slate-500 mt-1">
                    Daftar rekam jejak presensi masuk dan keluar harian Anda.
                </p>
            </div>
        </div>
    </x-slot>

    <!-- Filter Card -->
    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-xs">
        <form method="GET" action="{{ route('staff.riwayat.index') }}" class="flex flex-wrap items-center gap-4">
            <div class="flex items-center gap-2">
                <x-icon name="filter" class="w-4 h-4 text-slate-400" />
                <span class="text-xs font-semibold text-slate-700">Filter Periode:</span>
            </div>

            <!-- Month Select -->
            <div class="w-40">
                <select name="bulan" class="w-full px-3 py-1.5 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                    @php
                        $months = [
                            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                        ];
                    @endphp
                    @foreach($months as $key => $name)
                        <option value="{{ $key }}" {{ $bulan == $key ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Year Select -->
            <div class="w-28">
                <select name="tahun" class="w-full px-3 py-1.5 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                    @for($y = date('Y') - 1; $y <= date('Y') + 1; $y++)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-xs transition-colors">
                <x-icon name="search" class="w-3.5 h-3.5" />
                <span>Terapkan</span>
            </button>

            @if(request()->has('bulan') || request()->has('tahun'))
                <a href="{{ route('staff.riwayat.index') }}" class="text-xs text-slate-500 hover:text-slate-800 underline">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Attendance Records Table Container with Modal -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden" 
         x-data="{ modalOpen: false, modalImg: '', modalTitle: '', modalTime: '' }">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3.5">Tanggal</th>
                        <th class="px-6 py-3.5">Jam Masuk</th>
                        <th class="px-6 py-3.5 text-center">Foto Masuk</th>
                        <th class="px-6 py-3.5">Jam Keluar</th>
                        <th class="px-6 py-3.5 text-center">Foto Keluar</th>
                        <th class="px-6 py-3.5">Status Masuk</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($presensis as $p)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-6 py-4 font-medium text-slate-900 whitespace-nowrap">
                                <div class="font-semibold">{{ \Carbon\Carbon::parse($p->tanggal_masuk)->translatedFormat('l') }}</div>
                                <div class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($p->tanggal_masuk)->translatedFormat('d F Y') }}</div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap font-mono text-xs text-slate-800">
                                <span class="font-semibold">{{ substr($p->jam_masuk, 0, 5) }}</span>
                                <span class="text-slate-400">{{ substr($p->jam_masuk, 5, 3) }}</span>
                            </td>

                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                @if($p->foto_masuk)
                                    <button type="button" 
                                            @click="
                                                modalOpen = true; 
                                                modalImg = '{{ asset('storage/presensi/' . $p->foto_masuk) }}'; 
                                                modalTitle = 'Foto Presensi Masuk';
                                                modalTime = '{{ \Carbon\Carbon::parse($p->tanggal_masuk)->translatedFormat('d M Y') }} - Pukul {{ $p->jam_masuk }}';
                                            "
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 text-slate-700 text-xs font-medium transition-colors">
                                        <x-icon name="image" class="w-3.5 h-3.5" />
                                        <span>Lihat</span>
                                    </button>
                                @else
                                    <span class="text-xs text-slate-300">-</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap font-mono text-xs text-slate-800">
                                @if($p->jam_keluar)
                                    <span class="font-semibold">{{ substr($p->jam_keluar, 0, 5) }}</span>
                                    <span class="text-slate-400">{{ substr($p->jam_keluar, 5, 3) }}</span>
                                @else
                                    <span class="text-xs text-slate-400 font-sans italic">Belum Presensi Keluar</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                @if($p->foto_keluar)
                                    <button type="button" 
                                            @click="
                                                modalOpen = true; 
                                                modalImg = '{{ asset('storage/presensi/' . $p->foto_keluar) }}'; 
                                                modalTitle = 'Foto Presensi Keluar';
                                                modalTime = '{{ \Carbon\Carbon::parse($p->tanggal_keluar)->translatedFormat('d M Y') }} - Pukul {{ $p->jam_keluar }}';
                                            "
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 text-slate-700 text-xs font-medium transition-colors">
                                        <x-icon name="image" class="w-3.5 h-3.5" />
                                        <span>Lihat</span>
                                    </button>
                                @else
                                    <span class="text-xs text-slate-300">-</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($p->status_masuk === 'Terlambat')
                                    <x-badge type="danger" text="Terlambat" />
                                @else
                                    <x-badge type="success" text="Tepat Waktu" />
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400 text-xs">
                                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
                                    <x-icon name="calendar" class="w-6 h-6" />
                                </div>
                                <span class="font-medium text-slate-600 block">Tidak Ada Data Presensi</span>
                                <span class="text-slate-400">Tidak ditemukan catatan kehadiran pada bulan {{ $months[$bulan] ?? $bulan }} {{ $tahun }}.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($presensis->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $presensis->links() }}
            </div>
        @endif

        <!-- Photo Viewer Modal -->
        <div x-show="modalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div @click.away="modalOpen = false" class="bg-white rounded-xl max-w-md w-full p-5 shadow-xl border border-slate-200">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900" x-text="modalTitle"></h3>
                        <p class="text-[11px] text-slate-400 mt-0.5" x-text="modalTime"></p>
                    </div>
                    <button type="button" @click="modalOpen = false" class="text-slate-400 hover:text-slate-600">
                        <x-icon name="x" class="w-5 h-5" />
                    </button>
                </div>

                <div class="rounded-lg overflow-hidden bg-slate-100 flex items-center justify-center aspect-4/3 max-h-80 border border-slate-200">
                    <img :src="modalImg" alt="Foto Presensi" class="w-full h-full object-cover">
                </div>

                <div class="mt-4 flex justify-end">
                    <button type="button" @click="modalOpen = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition-colors">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
