<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Rekapitulasi Presensi - Presensi</title>
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; font-size: 12px; }
            table { page-break-inside: auto; }
            tr { page-break-inside: avoid; page-break-after: auto; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 p-4 sm:p-8">
    <div class="max-w-5xl mx-auto bg-white p-8 rounded-xl shadow-xs border border-slate-200">
        <!-- Action Buttons -->
        <div class="no-print flex justify-end gap-2 mb-6">
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow-xs transition-colors">
                <x-icon name="printer" class="w-4 h-4" />
                <span>Cetak / Simpan PDF</span>
            </button>
            <button onclick="window.close()" class="px-4 py-2 border border-slate-300 rounded-lg text-sm text-slate-700 hover:bg-slate-50">
                Tutup
            </button>
        </div>

        <!-- Official Letterhead Header -->
        <div class="text-center pb-6 border-b-2 border-slate-900 space-y-1">
            <h1 class="text-xl font-bold uppercase tracking-wider text-slate-900">LAPORAN REKAPITULASI PRESENSI PEGAWAI</h1>
            <p class="text-sm font-medium text-slate-600">SISTEM PRESENSI ONLINE BERBASIS GPS & KAMERA (PRESENSI)</p>
            <p class="text-xs text-slate-500">Periode: {{ \Carbon\Carbon::parse($startDate)->locale('id')->isoFormat('D MMMM Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->locale('id')->isoFormat('D MMMM Y') }}</p>
        </div>

        <!-- Meta Information -->
        <div class="my-4 text-xs text-slate-600 grid grid-cols-2 gap-2">
            <div>Tanggal Cetak: <span class="font-semibold">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y, HH:mm') }}</span></div>
            <div class="text-right">Total Catatan: <span class="font-semibold">{{ $presensis->count() }} Data</span></div>
        </div>

        <!-- Table Data -->
        <table class="w-full text-left text-xs border border-slate-300">
            <thead class="bg-slate-100 border-b border-slate-300 font-semibold text-slate-700">
                <tr>
                    <th class="p-2 border-r border-slate-300 w-8 text-center">No</th>
                    <th class="p-2 border-r border-slate-300">Tanggal</th>
                    <th class="p-2 border-r border-slate-300">NRG</th>
                    <th class="p-2 border-r border-slate-300">Nama Pegawai</th>
                    <th class="p-2 border-r border-slate-300">Lokasi Kantor</th>
                    <th class="p-2 border-r border-slate-300 text-center">Jam Masuk</th>
                    <th class="p-2 border-r border-slate-300 text-center">Jam Keluar</th>
                    <th class="p-2 text-center">Status Kehadiran</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-300">
                @forelse($presensis as $index => $p)
                    <tr>
                        <td class="p-2 border-r border-slate-300 text-center">{{ $index + 1 }}</td>
                        <td class="p-2 border-r border-slate-300 whitespace-nowrap">{{ $p->tanggal_masuk }}</td>
                        <td class="p-2 border-r border-slate-300 font-mono">{{ $p->pegawai?->nrg ?? '-' }}</td>
                        <td class="p-2 border-r border-slate-300 font-semibold">{{ $p->pegawai?->nama ?? '-' }}</td>
                        <td class="p-2 border-r border-slate-300">{{ $p->pegawai?->lokasi_presensi ?? '-' }}</td>
                        <td class="p-2 border-r border-slate-300 text-center font-mono">{{ $p->jam_masuk }}</td>
                        <td class="p-2 border-r border-slate-300 text-center font-mono">{{ $p->jam_keluar ?? '-' }}</td>
                        <td class="p-2 text-center font-semibold {{ $p->status_masuk === 'Terlambat' ? 'text-rose-700' : 'text-emerald-700' }}">
                            {{ $p->status_masuk }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="p-6 text-center text-slate-400">Tidak ada data kehadiran ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Signature Space -->
        <div class="mt-12 grid grid-cols-2 text-xs text-slate-700">
            <div></div>
            <div class="text-center space-y-16">
                <div>
                    <div>Mengetahui,</div>
                    <div class="font-semibold">Kepala Bagian Kepegawaian</div>
                </div>
                <div class="font-bold underline uppercase">( ............................................ )</div>
            </div>
        </div>
    </div>
</body>
</html>
