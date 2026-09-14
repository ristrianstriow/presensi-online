<x-app-layout title="Manajemen Akun Pengguna">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Manajemen Akun Pengguna</h1>
                <p class="text-sm text-slate-500 mt-1">Kelola kredensial login, hak akses (role), dan status aktif/nonaktif akun.</p>
            </div>
        </div>
    </x-slot>

    <div x-data="{
        editModalOpen: false,
        selectedUser: null,
        openEdit(user) {
            this.selectedUser = user;
            this.editModalOpen = true;
        }
    }" class="space-y-6">

        <!-- Search & Filter -->
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
            <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <div class="sm:col-span-2 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <x-icon name="search" class="w-4 h-4" />
                    </div>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Cari username, nama pegawai, atau NRG..." 
                           class="w-full pl-9 pr-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <select name="role" class="w-full py-2 px-3 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                        <option value="">Semua Role</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="staff" {{ request('role') === 'staff' ? 'selected' : '' }}>Staff</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <select name="status" class="w-full py-2 px-3 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    <button type="submit" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium rounded-lg transition-colors">
                        Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">Username</th>
                            <th class="py-3 px-4">Pegawai Terkait</th>
                            <th class="py-3 px-4">Role</th>
                            <th class="py-3 px-4">Status Akun</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($users as $user)
                            <tr class="hover:bg-slate-50/75 transition-colors">
                                <td class="py-3 px-4">
                                    <div class="font-mono text-sm font-bold text-slate-900">{{ $user->username }}</div>
                                    @if($user->id === Auth::id())
                                        <span class="text-[10px] text-indigo-600 font-semibold bg-indigo-50 px-1.5 py-0.5 rounded">Akun Anda</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-semibold text-slate-800">{{ $user->pegawai?->nama ?? '-' }}</div>
                                    <div class="text-xs text-slate-500 font-mono">{{ $user->pegawai?->nrg ?? '-' }} • {{ $user->pegawai?->jabatan ?? '-' }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="uppercase text-[11px] font-bold tracking-wider px-2 py-0.5 rounded {{ $user->role === 'admin' ? 'bg-indigo-50 text-indigo-700' : 'bg-emerald-50 text-emerald-700' }}">
                                        {{ $user->role }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <x-badge :status="$user->status" />
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <button type="button" 
                                            @click="openEdit({{ json_encode($user) }})"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold transition-colors">
                                        <x-icon name="edit" class="w-3.5 h-3.5" />
                                        <span>Kelola Akun</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-slate-400 text-sm">
                                    <x-icon name="shield" class="w-8 h-8 mx-auto mb-2 text-slate-300" />
                                    Tidak ada data akun user ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="p-4 border-t border-slate-200">
                    {{ $users->links() }}
                </div>
            @endif
        </div>

        <!-- Edit Modal -->
        <div x-show="editModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs">
            <div class="bg-white rounded-xl border border-slate-200 shadow-xl max-w-md w-full p-6 space-y-4" @click.outside="editModalOpen = false">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Kelola Akun Pengguna</h3>
                        <p class="text-xs text-slate-500" x-text="selectedUser ? selectedUser.username : ''"></p>
                    </div>
                    <button type="button" @click="editModalOpen = false" class="text-slate-400 hover:text-slate-600 p-1">
                        <x-icon name="x" class="w-5 h-5" />
                    </button>
                </div>

                <form :action="'{{ url('admin/users') }}/' + (selectedUser ? selectedUser.id : '')" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Role Pengguna</label>
                        <select name="role" x-model="selectedUser.role" class="mt-1 block w-full py-2 px-3 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                            <option value="staff">Staff</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Status Akun</label>
                        <select name="status" x-model="selectedUser.status" class="mt-1 block w-full py-2 px-3 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                        <p class="mt-1 text-xs text-slate-400">Akun nonaktif tidak akan dapat masuk ke sistem.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Reset Password (Opsional)</label>
                        <input type="password" name="password" placeholder="Kosongkan bila tidak ingin mengubah"
                               class="mt-1 block w-full py-2 px-3 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="editModalOpen = false" class="px-4 py-2 border border-slate-300 rounded-lg text-sm text-slate-700 hover:bg-slate-50">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
