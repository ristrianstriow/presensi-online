<x-app-layout title="Profil Administrator">
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Profil Administrator</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola data identitas dan keamanan akun administrator sistem.</p>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sidebar Profile Card -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-xs text-center">
                <div class="relative w-24 h-24 mx-auto rounded-full overflow-hidden border-4 border-slate-100 bg-slate-100 mb-4 shadow-inner">
                    @if($pegawai && $pegawai->foto && $pegawai->foto !== 'default.png')
                        <img src="{{ asset('storage/pegawai/' . $pegawai->foto) }}" alt="{{ $pegawai->nama }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-indigo-50 text-indigo-600">
                            <x-icon name="user" class="w-12 h-12" />
                        </div>
                    @endif
                </div>

                <h2 class="text-lg font-bold text-slate-900">{{ $pegawai->nama ?? $user->username }}</h2>
                <p class="text-xs text-slate-500 mt-0.5">{{ $pegawai->jabatan ?? 'Administrator Sistem' }}</p>

                <div class="mt-4 flex items-center justify-center gap-2">
                    <x-badge type="info" text="Admin" />
                    <x-badge type="success" text="Akun Aktif" />
                </div>

                <div class="mt-6 pt-6 border-t border-slate-100 text-left space-y-3 text-xs">
                    <div class="flex items-center justify-between text-slate-600">
                        <span class="text-slate-400">Username</span>
                        <span class="font-semibold text-slate-800">{{ $user->username }}</span>
                    </div>
                    <div class="flex items-center justify-between text-slate-600">
                        <span class="text-slate-400">Nomor Registrasi (NRG)</span>
                        <span class="font-semibold text-slate-800">{{ $pegawai->nrg ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between text-slate-600">
                        <span class="text-slate-400">Lokasi Penugasan</span>
                        <span class="font-semibold text-slate-800">{{ $pegawai->lokasi_presensi ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- Security Info Note -->
            <div class="bg-slate-50 rounded-xl border border-slate-200 p-4 text-xs text-slate-600 space-y-2">
                <div class="flex items-center gap-2 font-semibold text-slate-800">
                    <x-icon name="shield" class="w-4 h-4 text-indigo-600" />
                    <span>Keamanan Akun</span>
                </div>
                <p class="text-slate-500 leading-relaxed">
                    Pastikan kata sandi Anda memiliki minimal 6 karakter. Ubah kata sandi secara berkala untuk menjaga keamanan sistem presensi.
                </p>
            </div>
        </div>

        <!-- Main Form -->
        <div class="lg:col-span-2">
            <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Biodata Section -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <x-icon name="user" class="w-5 h-5 text-indigo-600" />
                            <h2 class="font-semibold text-slate-900 text-sm">Informasi Data Pribadi</h2>
                        </div>
                        <span class="text-xs text-slate-400">Data Pegawai</span>
                    </div>

                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="nama" class="block text-xs font-semibold text-slate-700 mb-1">
                                    Nama Lengkap <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" 
                                       id="nama" 
                                       name="nama" 
                                       value="{{ old('nama', $pegawai->nama ?? '') }}" 
                                       required 
                                       class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                                @error('nama')
                                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="no_handphone" class="block text-xs font-semibold text-slate-700 mb-1">
                                    Nomor Handphone / WhatsApp <span class="text-rose-500">*</span>
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
                        </div>

                        <div>
                            <label for="alamat" class="block text-xs font-semibold text-slate-700 mb-1">
                                Alamat Tempat Tinggal <span class="text-rose-500">*</span>
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

                        <div x-data="{ photoName: null, photoPreview: null }">
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
                                               photoName = $event.target.files[0].name;
                                               const reader = new FileReader();
                                               reader.onload = (e) => { photoPreview = e.target.result; };
                                               reader.readAsDataURL($event.target.files[0]);
                                           "
                                           class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                                    <p class="text-[11px] text-slate-400 mt-1">Format: JPG, PNG, JPEG. Maksimal ukuran 2MB.</p>
                                </div>
                            </div>
                            @error('foto')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Password Section -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <x-icon name="lock" class="w-5 h-5 text-indigo-600" />
                            <h2 class="font-semibold text-slate-900 text-sm">Ganti Kata Sandi</h2>
                        </div>
                        <span class="text-xs text-slate-400">Opsional</span>
                    </div>

                    <div class="p-6 space-y-4">
                        <p class="text-xs text-slate-500">Kosongkan bagian ini jika Anda tidak ingin mengubah kata sandi.</p>

                        <div>
                            <label for="current_password" class="block text-xs font-semibold text-slate-700 mb-1">
                                Kata Sandi Saat Ini
                            </label>
                            <input type="password" 
                                   id="current_password" 
                                   name="current_password" 
                                   autocomplete="current-password"
                                   placeholder="Masukkan kata sandi lama" 
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
