<x-guest-layout title="Masuk ke Akun">
    <form method="POST" action="{{ route('login.post') }}" class="space-y-5" x-data="{
        fill(username, password) {
            $refs.username.value = username;
            $refs.password.value = password;
        }
    }">
        @csrf

        <div>
            <label for="username" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Username</label>
            <div class="mt-1 relative rounded-md shadow-xs">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <x-icon name="user" class="w-5 h-5" />
                </div>
                <input id="username" 
                       name="username" 
                       type="text" 
                       x-ref="username"
                       value="{{ old('username') }}" 
                       required 
                       autofocus 
                       placeholder="Masukkan username Anda"
                       class="block w-full pl-10 pr-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white placeholder-slate-400">
            </div>
            @error('username')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Password</label>
            <div class="mt-1 relative rounded-md shadow-xs">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <x-icon name="shield" class="w-5 h-5" />
                </div>
                <input id="password" 
                       name="password" 
                       type="password" 
                       x-ref="password"
                       required 
                       placeholder="Masukkan kata sandi"
                       class="block w-full pl-10 pr-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white placeholder-slate-400">
            </div>
            @error('password')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="remember" class="w-4 h-4 rounded text-indigo-600 border-slate-300 focus:ring-indigo-500">
                <span class="text-xs text-slate-600">Ingat saya</span>
            </label>
        </div>

        <button type="submit" 
                class="w-full flex justify-center items-center gap-2 py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
            <span>Masuk ke Sistem</span>
            <x-icon name="chevron-right" class="w-4 h-4" />
        </button>

        <!-- Dummy Demo Credentials Helper -->
        <div class="pt-4 border-t border-slate-100">
            <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-2 text-center">Akun Uji Coba Cepat</div>
            <div class="grid grid-cols-2 gap-2 text-xs">
                <button type="button" 
                        @click="fill('admin', 'password123')"
                        class="p-2 border border-slate-200 rounded-lg text-left hover:border-indigo-500 hover:bg-indigo-50/50 transition-colors">
                    <div class="font-semibold text-slate-800">Admin</div>
                    <div class="text-slate-500 text-[11px]">admin / password123</div>
                </button>
                <button type="button" 
                        @click="fill('siti.aminah', 'password123')"
                        class="p-2 border border-slate-200 rounded-lg text-left hover:border-indigo-500 hover:bg-indigo-50/50 transition-colors">
                    <div class="font-semibold text-slate-800">Staff (Siti)</div>
                    <div class="text-slate-500 text-[11px]">siti.aminah / password123</div>
                </button>
            </div>
        </div>
    </form>
</x-guest-layout>
