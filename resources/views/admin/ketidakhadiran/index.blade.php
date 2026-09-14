<x-app-layout title="Pengajuan Izin / Ketidakhadiran">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Pengajuan Izin Pegawai</h1>
                <p class="text-sm text-slate-500 mt-1">Verifikasi dan kelola permohonan izin sakit, cuti, atau dinas luar staff.</p>
            </div>
        </div>
    </x-slot>

    <!-- Status Tabs & Search -->
    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <!-- Tabs -->
            <div class="flex flex-wrap gap-1 p-1 bg-slate-200/60 rounded-xl max-w-fit">
                <a href="{{ route('admin.ketidakhadiran.index', ['status' => 'menunggu', 'search' => request('search')]) }}" 
                   class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $status === 'menunggu' ? 'bg-white text-indigo-700 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    <span>Menunggu</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $status === 'menunggu' ? 'bg-amber-100 text-amber-800' : 'bg-slate-300 text-slate-700' }}">{{ $counts['menunggu'] }}</span>
                </a>

                <a href="{{ route('admin.ketidakhadiran.index', ['status' => 'disetujui', 'search' => request('search')]) }}" 
                   class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $status === 'disetujui' ? 'bg-white text-indigo-700 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    <span>Disetujui</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $status === 'disetujui' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-300 text-slate-700' }}">{{ $counts['disetujui'] }}</span>
                </a>

                <a href="{{ route('admin.ketidakhadiran.index', ['status' => 'ditolak', 'search' => request('search')]) }}" 
                   class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $status === 'ditolak' ? 'bg-white text-indigo-700 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    <span>Ditolak</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $status === 'ditolak' ? 'bg-rose-100 text-rose-800' : 'bg-slate-300 text-slate-700' }}">{{ $counts['ditolak'] }}</span>
                </a>

                <a href="{{ route('admin.ketidakhadiran.index', ['status' => 'semua', 'search' => request('search')]) }}" 
                   class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $status === 'semua' ? 'bg-white text-indigo-700 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    <span>Semua</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-slate-300 text-slate-700">{{ $counts['semua'] }}</span>
                </a>
            </div>

            <!-- Search -->
            <form method="GET" action="{{ route('admin.ketidakhadiran.index') }}" class="relative sm:w-72">
                <input type="hidden" name="status" value="{{ $status }}">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <x-icon name="search" class="w-4 h-4" />
                </div>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Cari nama atau NRG..." 
                       class="w-full pl-9 pr-3 py-1.5 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
            </form>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">Tanggal Izin</th>
                            <th class="py-3 px-4">Pegawai</th>
                            <th class="py-3 px-4">Jenis Keterangan</th>
                            <th class="py-3 px-4">Deskripsi / Alasan</th>
                            <th class="py-3 px-4">Lampiran Bukti</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($ketidakhadirans as $izin)
                            <tr class="hover:bg-slate-50/75 transition-colors">
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <div class="font-medium text-slate-900">{{ \Carbon\Carbon::parse($izin->tanggal)->locale('id')->isoFormat('D MMMM Y') }}</div>
                                    <div class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($izin->tanggal)->locale('id')->isoFormat('dddd') }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-semibold text-slate-900">{{ $izin->pegawai?->nama ?? '-' }}</div>
                                    <div class="text-xs text-slate-500">{{ $izin->pegawai?->jabatan }} • {{ $izin->pegawai?->nrg }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 text-slate-800">
                                        {{ $izin->keterangan }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-xs text-slate-600 max-w-xs">
                                    {{ $izin->deskripsi ?? '-' }}
                                </td>
                                <td class="py-3 px-4">
                                    @if($izin->file)
                                        <a href="{{ asset('storage/ketidakhadiran/' . $izin->file) }}" target="_blank"
                                           class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded text-xs font-medium text-indigo-700 bg-indigo-50 hover:bg-indigo-100 transition-colors">
                                            <x-icon name="file-text" class="w-3.5 h-3.5" />
                                            <span>Lihat File</span>
                                        </a>
                                    @else
                                        <span class="text-xs text-slate-400 italic">Tanpa lampiran</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    <x-badge :status="$izin->status_pengajuan" />
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <div class="inline-flex items-center justify-end gap-1.5">
                                        @if($izin->status_pengajuan !== 'disetujui')
                                            <form method="POST" action="{{ route('admin.ketidakhadiran.status', $izin->id) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status_pengajuan" value="disetujui">
                                                <button type="submit" 
                                                        title="Setujui Izin"
                                                        class="px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 text-xs font-semibold rounded-md transition-colors">
                                                    Setujui
                                                </button>
                                            </form>
                                        @endif

                                        @if($izin->status_pengajuan !== 'ditolak')
                                            <form method="POST" action="{{ route('admin.ketidakhadiran.status', $izin->id) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status_pengajuan" value="ditolak">
                                                <button type="submit" 
                                                        title="Tolak Izin"
                                                        class="px-2.5 py-1 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-semibold rounded-md transition-colors">
                                                    Tolak
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400 text-sm">
                                    <x-icon name="file-text" class="w-8 h-8 mx-auto mb-2 text-slate-300" />
                                    Tidak ada data pengajuan izin pada kategori ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($ketidakhadirans->hasPages())
                <div class="p-4 border-t border-slate-200">
                    {{ $ketidakhadirans->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
