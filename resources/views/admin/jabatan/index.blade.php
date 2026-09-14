<x-app-layout title="Data Jabatan">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Data Jabatan</h1>
                <p class="text-sm text-slate-500 mt-1">Kelola master data jabatan struktural dan fungsional instansi.</p>
            </div>
            <button type="button" 
                    @click="$dispatch('open-modal-add')"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow-xs transition-colors">
                <x-icon name="plus" class="w-4 h-4" />
                <span>Tambah Jabatan Baru</span>
            </button>
        </div>
    </x-slot>

    <div x-data="{
        addModalOpen: false,
        editModalOpen: false,
        editId: null,
        editNama: '',
        openEdit(id, nama) {
            this.editId = id;
            this.editNama = nama;
            this.editModalOpen = true;
        }
    }" 
    @open-modal-add.window="addModalOpen = true"
    class="space-y-6">

        <!-- Table Card -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4 w-16 text-center">No</th>
                            <th class="py-3 px-4">Nama Jabatan</th>
                            <th class="py-3 px-4">Jumlah Pegawai</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($jabatans as $index => $jabatan)
                            <tr class="hover:bg-slate-50/75 transition-colors">
                                <td class="py-3 px-4 text-center font-mono text-xs text-slate-500">{{ $index + 1 }}</td>
                                <td class="py-3 px-4 font-semibold text-slate-900">{{ $jabatan->jabatan }}</td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                                        <x-icon name="users" class="w-3.5 h-3.5 text-slate-400" />
                                        {{ $jabatan->total_pegawai }} pegawai
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <div class="inline-flex items-center gap-1">
                                        <button type="button" 
                                                @click="openEdit({{ $jabatan->id }}, '{{ addslashes($jabatan->jabatan) }}')"
                                                title="Ubah Jabatan"
                                                class="p-1.5 text-slate-500 hover:text-indigo-600 hover:bg-slate-100 rounded transition-colors">
                                            <x-icon name="edit" class="w-4 h-4" />
                                        </button>
                                        <form method="POST" action="{{ route('admin.jabatan.destroy', $jabatan->id) }}" 
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus jabatan {{ $jabatan->jabatan }}?')"
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    title="Hapus Jabatan"
                                                    class="p-1.5 text-slate-500 hover:text-rose-600 hover:bg-slate-100 rounded transition-colors">
                                                <x-icon name="trash" class="w-4 h-4" />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-slate-400 text-sm">
                                    <x-icon name="briefcase" class="w-8 h-8 mx-auto mb-2 text-slate-300" />
                                    Belum ada data jabatan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Modal -->
        <div x-show="addModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs">
            <div class="bg-white rounded-xl border border-slate-200 shadow-xl max-w-md w-full p-6 space-y-4" @click.outside="addModalOpen = false">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                    <h3 class="text-base font-bold text-slate-900">Tambah Jabatan Baru</h3>
                    <button type="button" @click="addModalOpen = false" class="text-slate-400 hover:text-slate-600 p-1">
                        <x-icon name="x" class="w-5 h-5" />
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.jabatan.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="new_jabatan" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Nama Jabatan <span class="text-rose-500">*</span></label>
                        <input type="text" id="new_jabatan" name="jabatan" required placeholder="Contoh: Manager Operasional"
                               class="mt-1 block w-full py-2 px-3 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="addModalOpen = false" class="px-4 py-2 border border-slate-300 rounded-lg text-sm text-slate-700 hover:bg-slate-50">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Modal -->
        <div x-show="editModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs">
            <div class="bg-white rounded-xl border border-slate-200 shadow-xl max-w-md w-full p-6 space-y-4" @click.outside="editModalOpen = false">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                    <h3 class="text-base font-bold text-slate-900">Ubah Nama Jabatan</h3>
                    <button type="button" @click="editModalOpen = false" class="text-slate-400 hover:text-slate-600 p-1">
                        <x-icon name="x" class="w-5 h-5" />
                    </button>
                </div>

                <form :action="'{{ url('admin/jabatan') }}/' + editId" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="edit_jabatan" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Nama Jabatan <span class="text-rose-500">*</span></label>
                        <input type="text" id="edit_jabatan" name="jabatan" x-model="editNama" required
                               class="mt-1 block w-full py-2 px-3 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="editModalOpen = false" class="px-4 py-2 border border-slate-300 rounded-lg text-sm text-slate-700 hover:bg-slate-50">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold">
                            Perbarui
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
