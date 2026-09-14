<x-app-layout title="Rekap Presensi">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Rekapitulasi Presensi Pegawai</h1>
                <p class="text-sm text-slate-500 mt-1">Laporan catatan kehadiran, jam masuk/keluar, bukti foto, dan status ketepatan waktu.</p>
            </div>
            <a href="{{ route('admin.rekap.print', request()->query()) }}" target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-sm font-semibold shadow-xs transition-colors">
                <x-icon name="printer" class="w-4 h-4" />
                <span>Cetak / Export Laporan</span>
            </a>
        </div>
    </x-slot>

    <div x-data="{
        previewModalOpen: false,
        previewImage: '',
        previewTitle: '',
        openPreview(imgUrl, title) {
            this.previewImage = imgUrl;
            this.previewTitle = title;
            this.previewModalOpen = true;
        }
    }" class="space-y-6">

        <!-- Filter Bar -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs">
            <form method="GET" action="{{ route('admin.rekap.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
                <div>
                    <label for="start_date" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">Dari Tanggal</label>
                    <input type="date" id="start_date" name="start_date" value="{{ $startDate }}"
                           class="w-full py-2 px-3 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label for="end_date" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">Sampai Tanggal</label>
                    <input type="date" id="end_date" name="end_date" value="{{ $endDate }}"
                           class="w-full py-2 px-3 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label for="id_pegawai" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">Pegawai</label>
                    <select id="id_pegawai" name="id_pegawai"
                            class="w-full py-2 px-3 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                        <option value="">Semua Pegawai</option>
                        @foreach($pegawais as $p)
                            <option value="{{ $p->id }}" {{ $pegawaiId == $p->id ? 'selected' : '' }}>{{ $p->nama }} ({{ $p->nrg }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="lokasi" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">Lokasi Kantor</label>
                    <select id="lokasi" name="lokasi"
                            class="w-full py-2 px-3 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                        <option value="">Semua Lokasi</option>
                        @foreach($lokasis as $l)
                            <option value="{{ $l->nama_lokasi }}" {{ $lokasiName == $l->nama_lokasi ? 'selected' : '' }}>{{ $l->nama_lokasi }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="flex-1 py-2 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-lg shadow-xs transition-colors">
                        Terapkan
                    </button>
                    <a href="{{ route('admin.rekap.index') }}" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium rounded-lg transition-colors" title="Reset filter">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">Tanggal</th>
                            <th class="py-3 px-4">Pegawai</th>
                            <th class="py-3 px-4">Lokasi Kantor</th>
                            <th class="py-3 px-4">Presensi Masuk</th>
                            <th class="py-3 px-4">Presensi Keluar</th>
                            <th class="py-3 px-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($presensis as $presensi)
                            <tr class="hover:bg-slate-50/75 transition-colors">
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <div class="font-medium text-slate-900">{{ \Carbon\Carbon::parse($presensi->tanggal_masuk)->locale('id')->isoFormat('D MMM Y') }}</div>
                                    <div class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($presensi->tanggal_masuk)->locale('id')->isoFormat('dddd') }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-semibold text-slate-900">{{ $presensi->pegawai?->nama ?? '-' }}</div>
                                    <div class="text-xs font-mono text-slate-400">{{ $presensi->pegawai?->nrg }}</div>
                                </td>
                                <td class="py-3 px-4 text-slate-600 text-xs">
                                    {{ $presensi->pegawai?->lokasi_presensi ?? '-' }}
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-3">
                                        @if($presensi->foto_masuk)
                                            <button type="button" 
                                                    @click="openPreview('{{ asset('storage/presensi/' . $presensi->foto_masuk) }}', 'Foto Masuk: {{ $presensi->pegawai?->nama }} - {{ $presensi->tanggal_masuk }} {{ $presensi->jam_masuk }}')"
                                                    class="w-9 h-9 rounded-lg overflow-hidden border border-slate-200 bg-slate-100 flex-shrink-0 hover:ring-2 hover:ring-indigo-500 transition-all cursor-pointer">
                                                <img src="{{ asset('storage/presensi/' . $presensi->foto_masuk) }}" alt="Foto Masuk" class="w-full h-full object-cover">
                                            </button>
                                        @endif
                                        <div class="font-mono text-xs font-semibold text-slate-800">{{ $presensi->jam_masuk }}</div>
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    @if($presensi->jam_keluar)
                                        <div class="flex items-center gap-3">
                                            @if($presensi->foto_keluar)
                                                <button type="button" 
                                                        @click="openPreview('{{ asset('storage/presensi/' . $presensi->foto_keluar) }}', 'Foto Keluar: {{ $presensi->pegawai?->nama }} - {{ $presensi->tanggal_keluar }} {{ $presensi->jam_keluar }}')"
                                                        class="w-9 h-9 rounded-lg overflow-hidden border border-slate-200 bg-slate-100 flex-shrink-0 hover:ring-2 hover:ring-indigo-500 transition-all cursor-pointer">
                                                    <img src="{{ asset('storage/presensi/' . $presensi->foto_keluar) }}" alt="Foto Keluar" class="w-full h-full object-cover">
                                                </button>
                                            @endif
                                            <div class="font-mono text-xs font-semibold text-slate-800">{{ $presensi->jam_keluar }}</div>
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-400 italic">Belum keluar</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <x-badge :status="$presensi->status_masuk" />
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400 text-sm">
                                    <x-icon name="calendar" class="w-8 h-8 mx-auto mb-2 text-slate-300" />
                                    Tidak ada data presensi pada rentang filter ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($presensis->hasPages())
                <div class="p-4 border-t border-slate-200">
                    {{ $presensis->links() }}
                </div>
            @endif
        </div>

        <!-- Image Preview Modal -->
        <div x-show="previewModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div class="bg-white rounded-xl border border-slate-200 shadow-xl max-w-lg w-full p-5 space-y-4" @click.outside="previewModalOpen = false">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                    <h3 class="text-sm font-bold text-slate-900 truncate" x-text="previewTitle"></h3>
                    <button type="button" @click="previewModalOpen = false" class="text-slate-400 hover:text-slate-600 p-1">
                        <x-icon name="x" class="w-5 h-5" />
                    </button>
                </div>

                <div class="w-full aspect-4/3 rounded-lg overflow-hidden bg-slate-900 flex items-center justify-center">
                    <img :src="previewImage" alt="Bukti Foto Presensi" class="w-full h-full object-contain">
                </div>

                <div class="flex justify-end">
                    <button type="button" @click="previewModalOpen = false" class="px-4 py-2 border border-slate-300 rounded-lg text-sm text-slate-700 hover:bg-slate-50">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
