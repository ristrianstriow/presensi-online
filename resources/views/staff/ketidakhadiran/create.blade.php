<x-app-layout title="Form Pengajuan Izin / Ketidakhadiran">
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('staff.ketidakhadiran.index') }}" class="p-2 rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-slate-800 transition-colors">
                <x-icon name="arrow-left" class="w-4 h-4" />
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Ajukan Izin / Ketidakhadiran</h1>
                <p class="text-sm text-slate-500 mt-0.5">Kirimkan permohonan ketidakhadiran kerja kepada administrator.</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <x-icon name="file-text" class="w-5 h-5 text-indigo-600" />
                    <h2 class="font-semibold text-slate-900 text-sm">Formulir Permohonan Izin</h2>
                </div>
                <span class="text-xs text-slate-400">Wajib Diisi Lengkap</span>
            </div>

            <form action="{{ route('staff.ketidakhadiran.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
                @csrf

                <!-- Jenis Keterangan -->
                <div>
                    <label for="keterangan" class="block text-xs font-semibold text-slate-700 mb-1">
                        Jenis Keterangan Izin <span class="text-rose-500">*</span>
                    </label>
                    <select id="keterangan" 
                            name="keterangan" 
                            required 
                            class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                        <option value="" disabled {{ old('keterangan') ? '' : 'selected' }}>Pilih jenis izin...</option>
                        <option value="Sakit" {{ old('keterangan') === 'Sakit' ? 'selected' : '' }}>Sakit</option>
                        <option value="Cuti" {{ old('keterangan') === 'Cuti' ? 'selected' : '' }}>Cuti</option>
                        <option value="Dinas Luar" {{ old('keterangan') === 'Dinas Luar' ? 'selected' : '' }}>Dinas Luar</option>
                        <option value="Keperluan Keluarga" {{ old('keterangan') === 'Keperluan Keluarga' ? 'selected' : '' }}>Keperluan Keluarga</option>
                        <option value="Lainnya" {{ old('keterangan') === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('keterangan')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tanggal Izin -->
                <div>
                    <label for="tanggal" class="block text-xs font-semibold text-slate-700 mb-1">
                        Tanggal Ketidakhadiran <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" 
                           id="tanggal" 
                           name="tanggal" 
                           value="{{ old('tanggal', date('Y-m-d')) }}" 
                           required 
                           class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                    @error('tanggal')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi / Alasan -->
                <div>
                    <label for="deskripsi" class="block text-xs font-semibold text-slate-700 mb-1">
                        Deskripsi / Penjelasan Alasan
                    </label>
                    <textarea id="deskripsi" 
                              name="deskripsi" 
                              rows="4" 
                              maxlength="225"
                              placeholder="Tuliskan keterangan detail alasan ketidakhadiran..." 
                              class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">{{ old('deskripsi') }}</textarea>
                    <div class="flex items-center justify-between mt-1 text-[11px] text-slate-400">
                        <span>Opsional namun disarankan untuk kejelasan administrasi.</span>
                        <span>Maksimal 225 karakter</span>
                    </div>
                    @error('deskripsi')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- File Lampiran -->
                <div>
                    <label for="file" class="block text-xs font-semibold text-slate-700 mb-1">
                        Lampiran Dokumen Bukti (Opsional)
                    </label>
                    <input type="file" 
                           id="file" 
                           name="file" 
                           accept=".pdf,.jpg,.jpeg,.png" 
                           class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer border border-slate-200 rounded-lg bg-slate-50/50">
                    <p class="text-[11px] text-slate-400 mt-1">
                        Format didukung: PDF, JPG, PNG (Surat dokter, surat dinas, dll). Maksimal ukuran file 3MB.
                    </p>
                    @error('file')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Buttons -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('staff.ketidakhadiran.index') }}" 
                       class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition-colors">
                        Batal
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center gap-2 px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-xs transition-colors focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        <x-icon name="check-circle" class="w-4 h-4" />
                        <span>Kirim Permohonan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
