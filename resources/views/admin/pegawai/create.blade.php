<x-app-layout title="Tambah Pegawai Baru">
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.pegawai.index') }}" class="p-2 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition-colors">
                <x-icon name="chevron-left" class="w-5 h-5" />
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Tambah Pegawai Baru</h1>
                <p class="text-sm text-slate-500 mt-0.5">Isi data identitas pegawai dan buat akun login sistem.</p>
            </div>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('admin.pegawai.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Card Data Pegawai -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-xs p-6 space-y-5">
            <h2 class="text-base font-semibold text-slate-900 pb-3 border-b border-slate-200">Informasi Pribadi & Penempatan</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="nrg" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Nomor Registrasi Guru / Pegawai (NRG) <span class="text-rose-500">*</span></label>
                    <input type="text" id="nrg" name="nrg" value="{{ old('nrg') }}" required placeholder="Contoh: NRG004"
                           class="mt-1 block w-full py-2 px-3 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @error('nrg') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="nama" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Nama Lengkap <span class="text-rose-500">*</span></label>
                    <input type="text" id="nama" name="nama" value="{{ old('nama') }}" required placeholder="Nama lengkap pegawai"
                           class="mt-1 block w-full py-2 px-3 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @error('nama') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="jenis_kelamin" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Jenis Kelamin <span class="text-rose-500">*</span></label>
                    <select id="jenis_kelamin" name="jenis_kelamin" required
                            class="mt-1 block w-full py-2 px-3 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="Laki-laki" {{ old('jenis_kelamin') === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ old('jenis_kelamin') === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    @error('jenis_kelamin') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="no_handphone" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Nomor Handphone / WhatsApp <span class="text-rose-500">*</span></label>
                    <input type="text" id="no_handphone" name="no_handphone" value="{{ old('no_handphone') }}" required placeholder="Contoh: 081234567890"
                           class="mt-1 block w-full py-2 px-3 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @error('no_handphone') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="jabatan" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Jabatan <span class="text-rose-500">*</span></label>
                    <select id="jabatan" name="jabatan" required
                            class="mt-1 block w-full py-2 px-3 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                        <option value="">Pilih Jabatan</option>
                        @foreach($jabatans as $j)
                            <option value="{{ $j->jabatan }}" {{ old('jabatan') === $j->jabatan ? 'selected' : '' }}>{{ $j->jabatan }}</option>
                        @endforeach
                    </select>
                    @error('jabatan') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="lokasi_presensi" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Lokasi Presensi Kantor <span class="text-rose-500">*</span></label>
                    <select id="lokasi_presensi" name="lokasi_presensi" required
                            class="mt-1 block w-full py-2 px-3 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                        <option value="">Pilih Lokasi Kantor</option>
                        @foreach($lokasis as $l)
                            <option value="{{ $l->nama_lokasi }}" {{ old('lokasi_presensi') === $l->nama_lokasi ? 'selected' : '' }}>{{ $l->nama_lokasi }} (Radius: {{ $l->radius }}m)</option>
                        @endforeach
                    </select>
                    @error('lokasi_presensi') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="alamat" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Alamat Lengkap <span class="text-rose-500">*</span></label>
                    <textarea id="alamat" name="alamat" rows="2" required placeholder="Alamat domisili pegawai"
                              class="mt-1 block w-full py-2 px-3 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('alamat') }}</textarea>
                    @error('alamat') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="foto" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Foto Profil (Opsional)</label>
                    <input type="file" id="foto" name="foto" accept="image/*"
                           class="mt-1 block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    <p class="mt-1 text-xs text-slate-400">Format: JPG, JPEG, PNG. Maksimal 2MB.</p>
                    @error('foto') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Card Akun Pengguna -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-xs p-6 space-y-5">
            <h2 class="text-base font-semibold text-slate-900 pb-3 border-b border-slate-200">Akun Pengguna (Login)</h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div>
                    <label for="username" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Username <span class="text-rose-500">*</span></label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" required placeholder="Contoh: budi.santoso"
                           class="mt-1 block w-full py-2 px-3 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @error('username') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Password <span class="text-rose-500">*</span></label>
                    <input type="password" id="password" name="password" required placeholder="Minimal 6 karakter"
                           class="mt-1 block w-full py-2 px-3 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @error('password') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="role" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Role Pengguna <span class="text-rose-500">*</span></label>
                    <select id="role" name="role" required
                            class="mt-1 block w-full py-2 px-3 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                        <option value="staff" {{ old('role', 'staff') === 'staff' ? 'selected' : '' }}>Staff</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                    @error('role') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.pegawai.index') }}" class="px-4 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                Batal
            </a>
            <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow-xs transition-colors">
                Simpan Pegawai
            </button>
        </div>
    </form>
</x-app-layout>
