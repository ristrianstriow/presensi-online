<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Login' }} - Presensi</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="h-full font-sans text-slate-800 antialiased flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-slate-100">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-indigo-600 text-white shadow-md mb-3">
            <x-icon name="map-pin" class="w-7 h-7" />
        </div>
        <h2 class="text-2xl font-bold tracking-tight text-slate-900">Presensi</h2>
        <p class="mt-1 text-sm text-slate-500">Sistem Presensi Online Berbasis GPS & Kamera</p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4">
        <div class="bg-white py-8 px-6 shadow-sm sm:rounded-xl sm:px-10 border border-slate-200">
            @if(session('error'))
                <div class="mb-5 p-4 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 flex items-start gap-3" role="alert">
                    <x-icon name="alert-circle" class="w-5 h-5 text-rose-600 flex-shrink-0 mt-0.5" />
                    <div class="text-sm">{{ session('error') }}</div>
                </div>
            @endif

            @if(session('success'))
                <div class="mb-5 p-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-start gap-3" role="alert">
                    <x-icon name="check-circle" class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" />
                    <div class="text-sm">{{ session('success') }}</div>
                </div>
            @endif

            {{ $slot }}
        </div>

        <div class="mt-6 text-center text-xs text-slate-400">
            Presensi Online System &copy; {{ date('Y') }}
        </div>
    </div>
</body>
</html>
