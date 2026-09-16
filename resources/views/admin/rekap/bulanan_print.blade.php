<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Rekapitulasi Presensi Bulanan - {{ $namaBulan }} {{ $tahun }}</title>
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; font-size: 11px; }
            table { page-break-inside: auto; }
            tr { page-break-inside: avoid; page-break-after: auto; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 p-4 sm:p-8 font-sans">
    <div class="max-w-6xl mx-auto bg-white p-8 rounded-xl shadow-xs border border-slate-200">
        <!-- Action Buttons -->
        <div class="no-print flex justify-end gap-2 mb-6">
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow-xs transition-colors cursor-pointer">
                <x-icon name="printer" class="w-4 h-4" />
                <span>Cetak / Simpan PDF</span>
            </button>
            <button onclick="window.close()" class="px-4 py-2 border border-slate-300 rounded-lg text-sm text-slate-700 hover:bg-slate-50 cursor-pointer">
                Tutup
            </button>
        </div>

        <!-- Official Letterhead Header -->
        <div class="text-center pb-5 border-b-2 border-slate-900 space-y-1">
            <h1 class="text-xl font-bold uppercase tracking-wider text-slate-900">LAPORAN REKAPITULASI PRESENSI BULANAN PEGAWAI</h1>
            <p class="text-sm font-medium text-slate-700">SISTEM PRESENSI ONLINE BERBASIS GPS & KAMERA (PRESENSI)</p>
            <p class="text-xs font-semibold text-slate-600">PERIODE: {{ strtoupper($namaBulan) }} {{ $tahun }}</p>
            @if($lokasiName)
                <p class="text-xs text-slate-500">Unit / Lokasi: {{ $lokasiName }}</p>
            @endif
        </div>

        <!-- Meta Information & Summary Box -->
        <div class="my-4 text-xs text-slate-600 grid grid-cols-2 gap-2">
            <div>
                <div>Tanggal Cetak: <span class="font-semibold">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y, HH:mm') }} WIB</span></div>
                <div>Jumlah Pegawai: <span class="font-semibold">{{ count($rekapPegawai) }} Orang</span></div>
            </div>
            <div class="text-right">
                <div>Total Presensi: <span class="font-semibold">{{ $stats['total_hadir'] }} Hari</span> (Tepat Waktu: {{ $stats['total_tepat_waktu'] }}, Terlambat: {{ $stats['total_terlambat'] }})</div>
                <div>Total Izin & Sakit: <span class="font-semibold">{{ $stats['total_izin_sakit'] }} Hari</span> (Sakit: {{ $stats['total_sakit'] }}, Cuti: {{ $stats['total_cuti'] }}, Izin: {{ $stats['total_izin'] }})</div>
            </div>
        </div>

        <!-- Table Data -->
        <table class="w-full text-left text-xs border border-slate-300">
            <thead class="bg-slate-100 border-b border-slate-300 font-semibold text-slate-700">
                <tr>
                    <th class="p-2 border-r border-slate-300 w-8 text-center">No</th>
                    <th class="p-2 border-r border-slate-300 w-24">NRG</th>
                    <th class="p-2 border-r border-slate-300">Nama Pegawai</th>
                    <th class="p-2 border-r border-slate-300">Jabatan</th>
                    <th class="p-2 border-r border-slate-300">Lokasi</th>
                    <th class="p-2 border-r border-slate-300 text-center w-14">Hadir</th>
                    <th class="p-2 border-r border-slate-300 text-center w-14">Tepat</th>
                    <th class="p-2 border-r border-slate-300 text-center w-14">Terlambat</th>
                    <th class="p-2 border-r border-slate-300 text-center w-12">Sakit</th>
                    <th class="p-2 border-r border-slate-300 text-center w-12">Cuti</th>
                    <th class="p-2 border-r border-slate-300 text-center w-12">Izin</th>
                    <th class="p-2 text-center w-16">% Kehadiran</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-300">
                @forelse($rekapPegawai as $index => $p)
                    <tr class="{{ $index % 2 === 1 ? 'bg-slate-50/60' : '' }}">
                        <td class="p-2 border-r border-slate-300 text-center font-mono">{{ $index + 1 }}</td>
                        <td class="p-2 border-r border-slate-300 font-mono">{{ $p->nrg }}</td>
                        <td class="p-2 border-r border-slate-300 font-semibold text-slate-900">{{ $p->nama }}</td>
                        <td class="p-2 border-r border-slate-300">{{ $p->jabatan }}</td>
                        <td class="p-2 border-r border-slate-300">{{ $p->lokasi_presensi }}</td>
                        <td class="p-2 border-r border-slate-300 text-center font-semibold text-slate-900">{{ $p->total_hadir }}</td>
                        <td class="p-2 border-r border-slate-300 text-center text-emerald-700 font-medium">{{ $p->tepat_waktu }}</td>
                        <td class="p-2 border-r border-slate-300 text-center {{ $p->terlambat > 0 ? 'text-amber-700 font-semibold' : 'text-slate-400' }}">{{ $p->terlambat }}</td>
                        <td class="p-2 border-r border-slate-300 text-center {{ $p->sakit > 0 ? 'text-blue-700 font-semibold' : 'text-slate-400' }}">{{ $p->sakit }}</td>
                        <td class="p-2 border-r border-slate-300 text-center {{ $p->cuti > 0 ? 'text-purple-700 font-semibold' : 'text-slate-400' }}">{{ $p->cuti }}</td>
                        <td class="p-2 border-r border-slate-300 text-center {{ $p->izin > 0 ? 'text-slate-700 font-semibold' : 'text-slate-400' }}">{{ $p->izin }}</td>
                        <td class="p-2 text-center font-bold font-mono {{ $p->persentase_kehadiran >= 80 ? 'text-emerald-700' : ($p->persentase_kehadiran >= 50 ? 'text-amber-700' : 'text-rose-700') }}">
                            {{ $p->persentase_kehadiran }}%
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" class="p-6 text-center text-slate-400">Tidak ada data kehadiran ditemukan pada filter ini.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-slate-100 border-t-2 border-slate-400 font-bold text-slate-900">
                <tr>
                    <td colspan="5" class="p-2 text-right border-r border-slate-300 uppercase tracking-wider text-xs">Total Keseluruhan:</td>
                    <td class="p-2 text-center border-r border-slate-300">{{ $stats['total_hadir'] }}</td>
                    <td class="p-2 text-center border-r border-slate-300 text-emerald-700">{{ $stats['total_tepat_waktu'] }}</td>
                    <td class="p-2 text-center border-r border-slate-300 text-amber-700">{{ $stats['total_terlambat'] }}</td>
                    <td class="p-2 text-center border-r border-slate-300 text-blue-700">{{ $stats['total_sakit'] }}</td>
                    <td class="p-2 text-center border-r border-slate-300 text-purple-700">{{ $stats['total_cuti'] }}</td>
                    <td class="p-2 text-center border-r border-slate-300 text-slate-700">{{ $stats['total_izin'] }}</td>
                    <td class="p-2 text-center text-indigo-700 font-mono">
                        {{ $stats['rata_rata_ketepatan'] }}% Tepat
                    </td>
                </tr>
            </tfoot>
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
