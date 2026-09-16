<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Presensi' }} - Aplikasi Presensi Online Berbasis GPS</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="h-full font-sans text-slate-800 antialiased flex flex-col" x-data="{ sidebarOpen: false }">
    
    <!-- Topbar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 flex-shrink-0">
        <div class="px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <button type="button" @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-slate-500 hover:text-slate-700 p-2 rounded-lg hover:bg-slate-100 focus:outline-none">
                    <x-icon name="menu" class="w-6 h-6" />
                </button>
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-lg bg-indigo-600 flex items-center justify-center text-white shadow-sm">
                        <x-icon name="map-pin" class="w-5 h-5" />
                    </div>
                    <div>
                        <span class="font-bold text-slate-900 tracking-tight text-lg">Presensi</span>
                        <span class="hidden sm:inline-block text-xs text-slate-400 font-medium ml-2 px-2 py-0.5 bg-slate-100 rounded">GPS & Kamera</span>
                    </div>
                </div>
            </div>

            <!-- User Menu -->
            <div class="flex items-center gap-3 sm:gap-4">
                @auth
                    @php
                        $user = Auth::user();
                        $pegawai = $user->pegawai;
                    @endphp
                    <div class="text-right hidden sm:block">
                        <div class="text-sm font-semibold text-slate-900 leading-none mb-1">{{ $pegawai->nama ?? $user->username }}</div>
                        <div class="text-xs text-slate-500 flex items-center justify-end gap-1.5">
                            <span>{{ $pegawai->nrg ?? $user->username }}</span>
                            <span>•</span>
                            <span class="uppercase text-[10px] tracking-wider px-1.5 py-0.5 rounded font-semibold {{ $user->role === 'admin' ? 'bg-indigo-50 text-indigo-700' : 'bg-emerald-50 text-emerald-700' }}">
                                {{ $user->role }}
                            </span>
                        </div>
                    </div>

                    <a href="{{ $user->role === 'admin' ? route('admin.profile') : route('staff.profile') }}" 
                       class="w-9 h-9 rounded-full bg-slate-100 border border-slate-200 overflow-hidden flex items-center justify-center hover:ring-2 hover:ring-indigo-500 transition-all">
                        @if($pegawai && $pegawai->foto && $pegawai->foto !== 'default.png')
                            <img src="{{ asset('storage/pegawai/' . $pegawai->foto) }}" alt="{{ $pegawai->nama }}" class="w-full h-full object-cover">
                        @else
                            <x-icon name="user" class="w-5 h-5 text-slate-600" />
                        @endif
                    </a>

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" title="Keluar dari sistem" 
                                class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors">
                            <x-icon name="log-out" class="w-5 h-5" />
                        </button>
                    </form>
                @endauth
            </div>
        </div>
    </header>

    <div class="flex flex-1 overflow-hidden">
        <!-- Sidebar Backdrop for Mobile -->
        <div x-show="sidebarOpen" 
             x-cloak 
             @click="sidebarOpen = false" 
             class="fixed inset-0 z-40 bg-slate-900/40 backdrop-blur-xs lg:hidden transition-opacity"></div>

        <!-- Sidebar Navigation -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
               class="fixed lg:static top-16 bottom-0 left-0 z-40 w-64 bg-white border-r border-slate-200 flex flex-col flex-shrink-0 transition-transform duration-200 ease-in-out">
            
            <div class="p-4 flex-1 overflow-y-auto space-y-6">
                @auth
                    @if(Auth::user()->role === 'admin')
                        <!-- Admin Navigation -->
                        <div>
                            <div class="px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Utama</div>
                            <nav class="space-y-1">
                                <a href="{{ route('admin.dashboard') }}" 
                                   class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                    <x-icon name="dashboard" class="w-5 h-5 {{ request()->routeIs('admin.dashboard') ? 'text-indigo-600' : 'text-slate-400' }}" />
                                    <span>Dashboard</span>
                                </a>
                            </nav>
                        </div>

                        <div>
                            <div class="px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Master Data</div>
                            <nav class="space-y-1">
                                <a href="{{ route('admin.pegawai.index') }}" 
                                   class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.pegawai.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                    <x-icon name="users" class="w-5 h-5 {{ request()->routeIs('admin.pegawai.*') ? 'text-indigo-600' : 'text-slate-400' }}" />
                                    <span>Data Pegawai</span>
                                </a>
                                <a href="{{ route('admin.jabatan.index') }}" 
                                   class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.jabatan.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                    <x-icon name="briefcase" class="w-5 h-5 {{ request()->routeIs('admin.jabatan.*') ? 'text-indigo-600' : 'text-slate-400' }}" />
                                    <span>Data Jabatan</span>
                                </a>
                                <a href="{{ route('admin.lokasi.index') }}" 
                                   class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.lokasi.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                    <x-icon name="map-pin" class="w-5 h-5 {{ request()->routeIs('admin.lokasi.*') ? 'text-indigo-600' : 'text-slate-400' }}" />
                                    <span>Lokasi Presensi</span>
                                </a>
                            </nav>
                        </div>

                        <div>
                            <div class="px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Presensi & Izin</div>
                            <nav class="space-y-1">
                                <a href="{{ route('admin.rekap.index') }}" 
                                   class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.rekap.index') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                    <x-icon name="calendar" class="w-5 h-5 {{ request()->routeIs('admin.rekap.index') ? 'text-indigo-600' : 'text-slate-400' }}" />
                                    <span>Rekap Harian</span>
                                </a>
                                <a href="{{ route('admin.rekap.bulanan') }}" 
                                   class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.rekap.bulanan*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                    <x-icon name="calendar" class="w-5 h-5 {{ request()->routeIs('admin.rekap.bulanan*') ? 'text-indigo-600' : 'text-slate-400' }}" />
                                    <span>Rekap Bulanan</span>
                                </a>
                                <a href="{{ route('admin.ketidakhadiran.index') }}" 
                                   class="flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.ketidakhadiran.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                    <div class="flex items-center gap-3">
                                        <x-icon name="file-text" class="w-5 h-5 {{ request()->routeIs('admin.ketidakhadiran.*') ? 'text-indigo-600' : 'text-slate-400' }}" />
                                        <span>Pengajuan Izin</span>
                                    </div>
                                    @php
                                        $pendingCount = \App\Models\Ketidakhadiran::where('status_pengajuan', 'menunggu')->count();
                                    @endphp
                                    @if($pendingCount > 0)
                                        <span class="px-2 py-0.5 text-xs font-bold bg-amber-100 text-amber-800 rounded-full">{{ $pendingCount }}</span>
                                    @endif
                                </a>
                            </nav>
                        </div>

                        <div>
                            <div class="px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Sistem</div>
                            <nav class="space-y-1">
                                <a href="{{ route('admin.users.index') }}" 
                                   class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                    <x-icon name="shield" class="w-5 h-5 {{ request()->routeIs('admin.users.*') ? 'text-indigo-600' : 'text-slate-400' }}" />
                                    <span>Kelola Akun User</span>
                                </a>
                                <a href="{{ route('admin.profile') }}" 
                                   class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.profile') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                    <x-icon name="settings" class="w-5 h-5 {{ request()->routeIs('admin.profile') ? 'text-indigo-600' : 'text-slate-400' }}" />
                                    <span>Profil Admin</span>
                                </a>
                            </nav>
                        </div>

                    @else
                        <!-- Staff Navigation -->
                        <div>
                            <div class="px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Menu Utama</div>
                            <nav class="space-y-1">
                                <a href="{{ route('staff.dashboard') }}" 
                                   class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('staff.dashboard') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                    <x-icon name="dashboard" class="w-5 h-5 {{ request()->routeIs('staff.dashboard') ? 'text-indigo-600' : 'text-slate-400' }}" />
                                    <span>Dashboard</span>
                                </a>
                                <a href="{{ route('staff.presensi.index') }}" 
                                   class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('staff.presensi.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                    <x-icon name="camera" class="w-5 h-5 {{ request()->routeIs('staff.presensi.*') ? 'text-indigo-600' : 'text-slate-400' }}" />
                                    <span>Presensi GPS & Foto</span>
                                </a>
                                <a href="{{ route('staff.riwayat.index') }}" 
                                   class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('staff.riwayat.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                    <x-icon name="calendar" class="w-5 h-5 {{ request()->routeIs('staff.riwayat.*') ? 'text-indigo-600' : 'text-slate-400' }}" />
                                    <span>Riwayat Presensi</span>
                                </a>
                            </nav>
                        </div>

                        <div>
                            <div class="px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Ketidakhadiran</div>
                            <nav class="space-y-1">
                                <a href="{{ route('staff.ketidakhadiran.create') }}" 
                                   class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('staff.ketidakhadiran.create') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                    <x-icon name="plus" class="w-5 h-5 {{ request()->routeIs('staff.ketidakhadiran.create') ? 'text-indigo-600' : 'text-slate-400' }}" />
                                    <span>Ajukan Izin / Cuti</span>
                                </a>
                                <a href="{{ route('staff.ketidakhadiran.index') }}" 
                                   class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('staff.ketidakhadiran.index') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                    <x-icon name="file-text" class="w-5 h-5 {{ request()->routeIs('staff.ketidakhadiran.index') ? 'text-indigo-600' : 'text-slate-400' }}" />
                                    <span>Status Pengajuan</span>
                                </a>
                            </nav>
                        </div>

                        <div>
                            <div class="px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Akun</div>
                            <nav class="space-y-1">
                                <a href="{{ route('staff.profile') }}" 
                                   class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('staff.profile') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                    <x-icon name="settings" class="w-5 h-5 {{ request()->routeIs('staff.profile') ? 'text-indigo-600' : 'text-slate-400' }}" />
                                    <span>Profil Saya</span>
                                </a>
                            </nav>
                        </div>
                    @endif
                @endauth
            </div>

            <!-- Footer Info -->
            <div class="p-4 border-t border-slate-200 text-xs text-slate-400 text-center">
                Presensi &copy; {{ date('Y') }}
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 overflow-y-auto bg-slate-50 p-4 sm:p-6 lg:p-8">
            <div class="max-w-7xl mx-auto space-y-6">
                <!-- Alerts / Flash Messages -->
                @if(session('success'))
                    <div class="p-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-start gap-3" role="alert">
                        <x-icon name="check-circle" class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" />
                        <div class="flex-1 text-sm">{{ session('success') }}</div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="p-4 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 flex items-start gap-3" role="alert">
                        <x-icon name="alert-circle" class="w-5 h-5 text-rose-600 flex-shrink-0 mt-0.5" />
                        <div class="flex-1 text-sm">{{ session('error') }}</div>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="p-4 rounded-lg bg-amber-50 border border-amber-200 text-amber-800 flex items-start gap-3" role="alert">
                        <x-icon name="alert-triangle" class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" />
                        <div class="flex-1 text-sm">{{ session('warning') }}</div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="p-4 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 flex items-start gap-3" role="alert">
                        <x-icon name="alert-circle" class="w-5 h-5 text-rose-600 flex-shrink-0 mt-0.5" />
                        <div class="flex-1 text-sm">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <!-- Page Header (Optional Slot) -->
                @isset($header)
                    <div class="pb-2 border-b border-slate-200">
                        {{ $header }}
                    </div>
                @endisset

                <!-- Main Content -->
                {{ $slot }}
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html>
