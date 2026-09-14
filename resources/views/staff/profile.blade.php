<x-app-layout title="Profil Saya">
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Profil Pegawai</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola data kontak pribadi dan keamanan akun presensi Anda.</p>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sidebar Employee Identity Card -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-xs text-center">
                <div class="relative w-24 h-24 mx-auto rounded-full overflow-hidden border-4 border-slate-100 bg-slate-100 mb-4 shadow-inner">
                    @if($pegawai && $pegawai->foto && $pegawai->foto !== 'default.png')
                        <img src="{{ asset('storage/pegawai/' . $pegawai->foto) }}" alt="{{ $pegawai->nama }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-emerald-50 text-emerald-600">
                            <x-icon name="user" class="w-12 h-12" />
                        </div>
                    @endif
                </div>

                <h2 class="text-lg font-bold text-slate-900">{{ $pegawai->nama ?? $user->username }}</h2>
                <p class="text-xs text-slate-500 mt-0.5">{{ $pegawai->jabatan ?? 'Staff Pegawai' }}</p>

                <div class="mt-4 flex items-center justify-center gap-2">
                    <x-badge type="success" text="Staff" />
                    <x-badge type="neutral" text="NRG: {{ $pegawai->nrg ?? '-' }}" />
                </div>

                <div class="mt-6 pt-6 border-t border-slate-100 text-left space-y-3 text-xs">
                    <div class="flex items-center justify-between text-slate-600">
                        <span class="text-slate-400">Username</span>
                        <span class="font-semibold text-slate-800">{{ $user->username }}</span>
                    </div>
                    <div class="flex items-center justify-between text-slate-600">
                        <span class="text-slate-400">Jenis Kelamin</span>
                        <span class="font-semibold text-slate-800">{{ $pegawai->jenis_kelamin ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between text-slate-600">
                        <span class="text-slate-400">Kantor Penugasan</span>
                        <span class="font-semibold text-slate-800">{{ $pegawai->lokasi_presensi ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between text-slate-600">
                        <span class="text-slate-400">Jadwal Masuk</span>
                        <span class="font-semibold text-slate-800">{{ substr($lokasi?->jam_masuk ?? '08:00', 0, 5) }} - {{ substr($lokasi?->jam_pulang ?? '17:00', 0, 5) }}</span>
                    </div>
                </div>
            </div>

            <!-- Notice note -->
            <div class="bg-slate-50 rounded-xl border border-slate-200 p-4 text-xs text-slate-600 space-y-2">
                <div class="flex items-center gap-2 font-semibold text-slate-800">
                    <x-icon name="info" class="w-4 h-4 text-indigo-600" />
                    <span>Catatan Administrasi</span>
                </div>
                <p class="text-slate-500 leading-relaxed">
                    Perubahan data pokok pegawai (Nama, NRG, Jabatan, dan Kantor Penugasan) hanya dapat dilakukan melalui Administrator Kepegawaian.
                </p>
            </div>
        </div>

        <!-- Update Form -->
        <div class="lg:col-span-2">
            <form action="{{ route('staff.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Contact & Address Section -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <x-icon name="user" class="w-5 h-5 text-indigo-600" />
                            <h2 class="font-semibold text-slate-900 text-sm">Kontak & Alamat Tempat Tinggal</h2>
                        </div>
                        <span class="text-xs text-slate-400">Data Pegawai</span>
                    </div>

                    <div class="p-6 space-y-4">
                        <div>
                            <label for="no_handphone" class="block text-xs font-semibold text-slate-700 mb-1">
                                Nomor Handphone / WhatsApp Aktif <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" 
                                   id="no_handphone" 
                                   name="no_handphone" 
                                   value="{{ old('no_handphone', $pegawai->no_handphone ?? '') }}" 
                                   required 
                                   class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                            @error('no_handphone')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="alamat" class="block text-xs font-semibold text-slate-700 mb-1">
                                Alamat Domisili / Tempat Tinggal <span class="text-rose-500">*</span>
                            </label>
                            <textarea id="alamat" 
                                      name="alamat" 
                                      rows="3" 
                                      required 
                                      class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">{{ old('alamat', $pegawai->alamat ?? '') }}</textarea>
                            @error('alamat')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Photo Upload with Preview -->
                        <div x-data="{ photoPreview: null }">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Foto Profil Baru</label>
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-lg bg-slate-100 border border-slate-200 overflow-hidden flex-shrink-0 flex items-center justify-center">
                                    <template x-if="photoPreview">
                                        <img :src="photoPreview" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!photoPreview">
                                        @if($pegawai && $pegawai->foto && $pegawai->foto !== 'default.png')
                                            <img src="{{ asset('storage/pegawai/' . $pegawai->foto) }}" class="w-full h-full object-cover">
                                        @else
                                            <x-icon name="image" class="w-6 h-6 text-slate-400" />
                                        @endif
                                    </template>
                                </div>
                                <div class="flex-1">
                                    <input type="file" 
                                           id="foto" 
                                           name="foto" 
                                           accept="image/*" 
                                           @change="
                                               const reader = new FileReader();
                                               reader.onload = (e) => { photoPreview = e.target.result; };
                                               reader.readAsDataURL($event.target.files[0]);
                                           "
                                           class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                                    <p class="text-[11px] text-slate-400 mt-1">Format: JPG, PNG, JPEG. Ukuran maksimal 2MB.</p>
                                </div>
                            </div>
                            @error('foto')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Password Change Section -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <x-icon name="lock" class="w-5 h-5 text-indigo-600" />
                            <h2 class="font-semibold text-slate-900 text-sm">Ganti Kata Sandi</h2>
                        </div>
                        <span class="text-xs text-slate-400">Opsional</span>
                    </div>

                    <div class="p-6 space-y-4">
                        <p class="text-xs text-slate-500">Kosongkan bagian kata sandi jika Anda tidak ingin mengubahnya.</p>

                        <div>
                            <label for="current_password" class="block text-xs font-semibold text-slate-700 mb-1">
                                Kata Sandi Saat Ini
                            </label>
                            <input type="password" 
                                   id="current_password" 
                                   name="current_password" 
                                   autocomplete="current-password"
                                   placeholder="Masukkan kata sandi lama Anda" 
                                   class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                            @error('current_password')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="new_password" class="block text-xs font-semibold text-slate-700 mb-1">
                                    Kata Sandi Baru
                                </label>
                                <input type="password" 
                                       id="new_password" 
                                       name="new_password" 
                                       autocomplete="new-password"
                                       placeholder="Minimal 6 karakter" 
                                       class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                                @error('new_password')
                                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="new_password_confirmation" class="block text-xs font-semibold text-slate-700 mb-1">
                                    Konfirmasi Kata Sandi Baru
                                </label>
                                <input type="password" 
                                       id="new_password_confirmation" 
                                       name="new_password_confirmation" 
                                       autocomplete="new-password"
                                       placeholder="Ulangi kata sandi baru" 
                                       class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end">
                        <button type="submit" 
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-xs transition-colors focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            <x-icon name="check-circle" class="w-4 h-4" />
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
