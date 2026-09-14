@props([
    'status' => '',
])

@php
    $normalized = strtolower(trim($status));
    $classes = match ($normalized) {
        'disetujui', 'aktif', 'tepat waktu' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'menunggu' => 'bg-amber-50 text-amber-700 border-amber-200',
        'ditolak', 'nonaktif', 'terlambat' => 'bg-rose-50 text-rose-700 border-rose-200',
        default => 'bg-slate-50 text-slate-700 border-slate-200',
    };
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-md border ' . $classes]) }}>
    <span class="w-1.5 h-1.5 rounded-full {{ match($normalized) {
        'disetujui', 'aktif', 'tepat waktu' => 'bg-emerald-500',
        'menunggu' => 'bg-amber-500',
        'ditolak', 'nonaktif', 'terlambat' => 'bg-rose-500',
        default => 'bg-slate-400',
    } }}"></span>
    {{ ucfirst($status) }}
</span>
