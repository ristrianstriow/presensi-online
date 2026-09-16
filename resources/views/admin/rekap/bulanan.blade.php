<x-app-layout title="Rekap Presensi Bulanan">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Rekapitulasi Presensi Bulanan</h1>
                <p class="text-sm text-slate-500 mt-1">Akumulasi catatan kehadiran, ketepatan waktu, dan izin per pegawai periode {{ $namaBulan }} {{ $tahun }}.</p>
            </div>
            <a href="{{ route('admin.rekap.bulanan.print', request()->query()) }}" target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-sm font-semibold shadow-xs transition-colors">
                <x-icon name="printer" class="w-4 h-4" />
                <span>Cetak Rekap Bulanan</span>
            </a>
        </div>
    </x-slot>

    <div x-data="{
        detailModalOpen: false,
        detailLoading: false,
        detailData: null,
        previewModalOpen: false,
        previewImage: '',
        previewTitle: '',

        async openDetail(pegawaiId) {
            this.detailModalOpen = true;
            this.detailLoading = true;
            this.detailData = null;
            try {
                const res = await fetch(`{{ url('admin/rekap/bulanan') }}/${pegawaiId}/detail?bulan={{ $bulan }}&tahun={{ $tahun }}`);
                if (!res.ok) throw new Error('Gagal mengambil data');
                this.detailData = await res.json();
            } catch (err) {
                alert('Gagal memuat rincian presensi: ' + err.message);
                this.detailModalOpen = false;
            } finally {
                this.detailLoading = false;
            }
        },

        openPhotoPreview(imgUrl, title) {
            this.previewImage = imgUrl;
            this.previewTitle = title;
            this.previewModalOpen = true;
        }
    }" class="space-y-6">

        <!-- Navigation Tabs: Harian vs Bulanan -->
        <div class="flex border-b border-slate-200">
            <a href="{{ route('admin.rekap.index') }}"
               class="py-3 px-5 text-sm font-medium border-b-2 border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300 transition-all flex items-center gap-2">
                <x-icon name="calendar" class="w-4 h-4 text-slate-400" />
                <span>Rekap Harian (Log Presensi)</span>
            </a>
            <a href="{{ route('admin.rekap.bulanan') }}"
               class="py-3 px-5 text-sm font-semibold border-b-2 border-indigo-600 text-indigo-600 transition-all flex items-center gap-2">
                <x-icon name="calendar" class="w-4 h-4 text-indigo-600" />
                <span>Rekap Bulanan (Per Pegawai)</span>
            </a>
        </div>

        <!-- Metric Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Kehadiran</p>
                        <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $stats['total_hadir'] }} <span class="text-xs font-normal text-slate-500">kali masuk</span></h3>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600">
                        <x-icon name="check-circle" class="w-5 h-5" />
                    </div>
                </div>
                <p class="text-xs text-slate-500 mt-2">Dari {{ $stats['total_pegawai'] }} pegawai di periode ini</p>
            </div>

            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Tepat Waktu</p>
                        <h3 class="text-2xl font-bold text-emerald-600 mt-1">{{ $stats['total_tepat_waktu'] }} <span class="text-xs font-normal text-slate-500">({{ $stats['rata_rata_ketepatan'] }}%)</span></h3>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600">
                        <x-icon name="clock" class="w-5 h-5" />
                    </div>
                </div>
                <p class="text-xs text-slate-500 mt-2">Tingkat disiplin jam masuk kerja</p>
            </div>

            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Terlambat</p>
                        <h3 class="text-2xl font-bold text-amber-600 mt-1">{{ $stats['total_terlambat'] }} <span class="text-xs font-normal text-slate-500">kali</span></h3>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600">
                        <x-icon name="alert-triangle" class="w-5 h-5" />
                    </div>
                </div>
                <p class="text-xs text-slate-500 mt-2">Presensi di atas jam masuk kantor</p>
            </div>

            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Izin & Sakit Disetujui</p>
                        <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $stats['total_izin_sakit'] }} <span class="text-xs font-normal text-slate-500">hari</span></h3>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600">
                        <x-icon name="file-text" class="w-5 h-5" />
                    </div>
                </div>
                <p class="text-xs text-slate-500 mt-2">Sakit: {{ $stats['total_sakit'] }} | Cuti: {{ $stats['total_cuti'] }} | Izin: {{ $stats['total_izin'] }}</p>
            </div>
        </div>

        <!-- Filter Bar: Bulan, Tahun, Pegawai, Lokasi -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs">
            <form method="GET" action="{{ route('admin.rekap.bulanan') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
                <div>
                    <label for="bulan" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">Pilih Bulan</label>
                    <select id="bulan" name="bulan"
                            class="w-full py-2 px-3 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                        @foreach($daftarBulan as $num => $nama)
                            <option value="{{ $num }}" {{ $bulan == $num ? 'selected' : '' }}>{{ $nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="tahun" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">Pilih Tahun</label>
                    <select id="tahun" name="tahun"
                            class="w-full py-2 px-3 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                        @foreach($daftarTahun as $thn)
                            <option value="{{ $thn }}" {{ $tahun == $thn ? 'selected' : '' }}>{{ $thn }}</option>
                        @endforeach
                    </select>
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
                    <a href="{{ route('admin.rekap.bulanan') }}" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium rounded-lg transition-colors" title="Reset filter">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Table Card: Rekap Per Pegawai -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 bg-slate-50/50">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Rekapitulasi Kehadiran Pegawai</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Periode: <span class="font-semibold text-indigo-700">{{ $namaBulan }} {{ $tahun }}</span></p>
                </div>
                <div class="text-xs text-slate-500">
                    Menampilkan <span class="font-semibold text-slate-900">{{ count($rekapPegawai) }}</span> pegawai
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4 w-12 text-center">No</th>
                            <th class="py-3 px-4">Pegawai</th>
                            <th class="py-3 px-4">Lokasi Kantor</th>
                            <th class="py-3 px-4 text-center">Hadir</th>
                            <th class="py-3 px-4 text-center">Tepat Waktu</th>
                            <th class="py-3 px-4 text-center">Terlambat</th>
                            <th class="py-3 px-4 text-center">Sakit</th>
                            <th class="py-3 px-4 text-center">Cuti</th>
                            <th class="py-3 px-4 text-center">Izin</th>
                            <th class="py-3 px-4 text-center">Persentase</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($rekapPegawai as $index => $pegawai)
                            <tr class="hover:bg-slate-50/75 transition-colors">
                                <td class="py-3 px-4 text-center text-xs text-slate-400 font-mono">
                                    {{ $index + 1 }}
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-semibold text-slate-900">{{ $pegawai->nama }}</div>
                                    <div class="text-xs font-mono text-slate-500">{{ $pegawai->nrg }} &bull; {{ $pegawai->jabatan }}</div>
                                </td>
                                <td class="py-3 px-4 text-xs text-slate-600">
                                    {{ $pegawai->lokasi_presensi }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        {{ $pegawai->total_hadir }} hari
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">
                                        {{ $pegawai->tepat_waktu }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if($pegawai->terlambat > 0)
                                        <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700">
                                            {{ $pegawai->terlambat }}
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400">0</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if($pegawai->sakit > 0)
                                        <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700">
                                            {{ $pegawai->sakit }}
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400">0</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if($pegawai->cuti > 0)
                                        <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-semibold bg-purple-50 text-purple-700">
                                            {{ $pegawai->cuti }}
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400">0</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if($pegawai->izin > 0)
                                        <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                                            {{ $pegawai->izin }}
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400">0</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <div class="w-14 bg-slate-100 rounded-full h-2 overflow-hidden">
                                            <div class="h-2 rounded-full {{ $pegawai->persentase_kehadiran >= 80 ? 'bg-emerald-500' : ($pegawai->persentase_kehadiran >= 50 ? 'bg-amber-500' : 'bg-rose-500') }}"
                                                 style="width: {{ $pegawai->persentase_kehadiran }}%"></div>
                                        </div>
                                        <span class="text-xs font-bold text-slate-700 font-mono">{{ $pegawai->persentase_kehadiran }}%</span>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <button type="button"
                                            @click="openDetail({{ $pegawai->id }})"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 hover:text-indigo-600 text-xs font-semibold transition-colors shadow-2xs">
                                        <x-icon name="eye" class="w-3.5 h-3.5 text-slate-400" />
                                        <span>Detail Harian</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="py-12 text-center text-slate-400 text-sm">
                                    <x-icon name="calendar" class="w-8 h-8 mx-auto mb-2 text-slate-300" />
                                    Tidak ada data pegawai yang sesuai dengan filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Detail Presensi Harian Pegawai -->
        <div x-show="detailModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div class="bg-white rounded-xl border border-slate-200 shadow-xl max-w-3xl w-full max-h-[90vh] flex flex-col overflow-hidden"
                 @click.outside="detailModalOpen = false">
                <!-- Modal Header -->
                <div class="p-5 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                            <span x-text="detailData ? detailData.pegawai.nama : 'Memuat...'"></span>
                            <span class="text-xs font-normal text-slate-500 font-mono" x-text="detailData ? '(' + detailData.pegawai.nrg + ')' : ''"></span>
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Rincian Presensi Hari-demi-Hari &bull; Periode: <span class="font-semibold text-indigo-700" x-text="detailData ? detailData.periode : ''"></span>
                        </p>
                    </div>
                    <button type="button" @click="detailModalOpen = false" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-200/60 transition-colors">
                        <x-icon name="x" class="w-5 h-5" />
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-5 overflow-y-auto flex-1 space-y-4">
                    <!-- Loading state -->
                    <template x-if="detailLoading">
                        <div class="py-12 text-center text-slate-500 text-sm flex flex-col items-center justify-center gap-3">
                            <x-icon name="refresh" class="w-6 h-6 animate-spin text-indigo-600" />
                            <span>Memuat catatan presensi harian...</span>
                        </div>
                    </template>

                    <!-- Data table -->
                    <template x-if="!detailLoading && detailData">
                        <div class="border border-slate-200 rounded-lg overflow-hidden">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-slate-50 uppercase font-semibold text-slate-600 border-b border-slate-200">
                                    <tr>
                                        <th class="py-2.5 px-3">Tanggal</th>
                                        <th class="py-2.5 px-3">Hari</th>
                                        <th class="py-2.5 px-3 text-center">Status</th>
                                        <th class="py-2.5 px-3 text-center">Jam Masuk</th>
                                        <th class="py-2.5 px-3 text-center">Jam Keluar</th>
                                        <th class="py-2.5 px-3 text-center">Bukti Foto</th>
                                        <th class="py-2.5 px-3">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <template x-for="item in detailData.details" :key="item.tanggal">
                                        <tr :class="item.is_weekend ? 'bg-slate-50/50 text-slate-400' : 'hover:bg-slate-50/60'">
                                            <td class="py-2 px-3 font-mono font-medium" x-text="item.tanggal"></td>
                                            <td class="py-2 px-3" x-text="item.hari"></td>
                                            <td class="py-2 px-3 text-center">
                                                <span class="inline-block px-2 py-0.5 rounded text-[11px] font-semibold"
                                                      :class="{
                                                          'bg-emerald-50 text-emerald-700 border border-emerald-200': item.status_label === 'Tepat Waktu',
                                                          'bg-amber-50 text-amber-700 border border-amber-200': item.status_label === 'Terlambat',
                                                          'bg-blue-50 text-blue-700 border border-blue-200': item.status === 'izin' && item.status_label.toLowerCase() === 'sakit',
                                                          'bg-purple-50 text-purple-700 border border-purple-200': item.status === 'izin' && item.status_label.toLowerCase() === 'cuti',
                                                          'bg-indigo-50 text-indigo-700 border border-indigo-200': item.status === 'izin' && !['sakit','cuti'].includes(item.status_label.toLowerCase()),
                                                          'bg-slate-100 text-slate-500': item.status === 'libur',
                                                          'bg-rose-50 text-rose-700 border border-rose-200': item.status === 'alpa'
                                                      }"
                                                      x-text="item.status_label">
                                                </span>
                                            </td>
                                            <td class="py-2 px-3 text-center font-mono" x-text="item.jam_masuk"></td>
                                            <td class="py-2 px-3 text-center font-mono" x-text="item.jam_keluar"></td>
                                            <td class="py-2 px-3 text-center">
                                                <div class="flex items-center justify-center gap-1">
                                                    <template x-if="item.foto_masuk">
                                                        <button type="button"
                                                                @click="openPhotoPreview(item.foto_masuk, 'Foto Masuk - ' + item.tanggal)"
                                                                class="w-6 h-6 rounded overflow-hidden border border-slate-200 hover:ring-2 hover:ring-indigo-500 cursor-pointer" title="Lihat Foto Masuk">
                                                            <img :src="item.foto_masuk" class="w-full h-full object-cover">
                                                        </button>
                                                    </template>
                                                    <template x-if="item.foto_keluar">
                                                        <button type="button"
                                                                @click="openPhotoPreview(item.foto_keluar, 'Foto Keluar - ' + item.tanggal)"
                                                                class="w-6 h-6 rounded overflow-hidden border border-slate-200 hover:ring-2 hover:ring-indigo-500 cursor-pointer" title="Lihat Foto Keluar">
                                                            <img :src="item.foto_keluar" class="w-full h-full object-cover">
                                                        </button>
                                                    </template>
                                                    <template x-if="!item.foto_masuk && !item.foto_keluar">
                                                        <span class="text-slate-300">-</span>
                                                    </template>
                                                </div>
                                            </td>
                                            <td class="py-2 px-3 text-slate-500 truncate max-w-xs" x-text="item.keterangan || '-'"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </template>
                </div>

                <!-- Modal Footer -->
                <div class="p-4 border-t border-slate-200 flex justify-end bg-slate-50">
                    <button type="button" @click="detailModalOpen = false" class="px-4 py-2 border border-slate-300 rounded-lg text-sm text-slate-700 hover:bg-white transition-colors">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

        <!-- Photo Preview Modal -->
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
