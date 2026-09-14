<x-app-layout title="Pengajuan Izin & Ketidakhadiran">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Status Pengajuan Izin</h1>
                <p class="text-sm text-slate-500 mt-1">
                    Pantau status permohonan izin sakit, cuti, atau dinas luar yang telah diajukan.
                </p>
            </div>

            <a href="{{ route('staff.ketidakhadiran.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-xs transition-colors">
                <x-icon name="plus" class="w-4 h-4" />
                <span>Ajukan Izin Baru</span>
            </a>
        </div>
    </x-slot>

    <!-- Table of Requests Card -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3.5">Tanggal Izin</th>
                        <th class="px-6 py-3.5">Jenis Keterangan</th>
                        <th class="px-6 py-3.5">Deskripsi / Alasan</th>
                        <th class="px-6 py-3.5 text-center">Dokumen Lampiran</th>
                        <th class="px-6 py-3.5">Status Pengajuan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($ketidakhadirans as $item)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-6 py-4 font-medium text-slate-900 whitespace-nowrap">
                                <div class="font-semibold">{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('l') }}</div>
                                <div class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}</div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-md text-xs font-semibold {{ $item->keterangan === 'Sakit' ? 'bg-rose-50 text-rose-700 border border-rose-100' : ($item->keterangan === 'Cuti' ? 'bg-amber-50 text-amber-700 border border-amber-100' : 'bg-indigo-50 text-indigo-700 border border-indigo-100') }}">
                                    {{ $item->keterangan }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-xs text-slate-600 max-w-xs leading-relaxed">
                                {{ $item->deskripsi ?? '-' }}
                            </td>

                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                @if($item->file)
                                    <a href="{{ asset('storage/ketidakhadiran/' . $item->file) }}" 
                                       target="_blank" 
                                       class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium transition-colors">
                                        <x-icon name="file" class="w-3.5 h-3.5 text-slate-500" />
                                        <span>Unduh Berkas</span>
                                    </a>
                                @else
                                    <span class="text-xs text-slate-300">Tidak Ada</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($item->status_pengajuan === 'disetujui')
                                    <x-badge type="success" text="Disetujui" />
                                @elseif($item->status_pengajuan === 'ditolak')
                                    <x-badge type="danger" text="Ditolak" />
                                @else
                                    <x-badge type="warning" text="Menunggu Persetujuan" />
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400 text-xs">
                                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
                                    <x-icon name="file-text" class="w-6 h-6" />
                                </div>
                                <span class="font-medium text-slate-600 block">Belum Ada Pengajuan Izin</span>
                                <span class="text-slate-400">Anda belum pernah mengajukan permohonan izin atau cuti.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($ketidakhadirans->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $ketidakhadirans->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
