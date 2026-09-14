<x-app-layout title="Tambah Lokasi Presensi">
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.lokasi.index') }}" class="p-2 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition-colors">
                <x-icon name="chevron-left" class="w-5 h-5" />
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Tambah Lokasi Presensi</h1>
                <p class="text-sm text-slate-500 mt-0.5">Atur titik koordinat GPS kantor, batas radius, dan ketentuan jam kerja.</p>
            </div>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('admin.lokasi.store') }}" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Form Details -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs p-6 space-y-4">
                <h2 class="text-base font-semibold text-slate-900 pb-3 border-b border-slate-200">Informasi Lokasi & Waktu</h2>

                <div>
                    <label for="nama_lokasi" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Nama Lokasi <span class="text-rose-500">*</span></label>
                    <input type="text" id="nama_lokasi" name="nama_lokasi" value="{{ old('nama_lokasi') }}" required placeholder="Contoh: SMK Tunas Media"
                           class="mt-1 block w-full py-2 px-3 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @error('nama_lokasi') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="alamat_lokasi" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Alamat Lengkap <span class="text-rose-500">*</span></label>
                    <textarea id="alamat_lokasi" name="alamat_lokasi" rows="2" required placeholder="Alamat lengkap instansi/kantor"
                              class="mt-1 block w-full py-2 px-3 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('alamat_lokasi') }}</textarea>
                    @error('alamat_lokasi') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="tipe_lokasi" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Tipe Lokasi <span class="text-rose-500">*</span></label>
                        <input type="text" id="tipe_lokasi" name="tipe_lokasi" value="{{ old('tipe_lokasi', 'Kantor') }}" required placeholder="Contoh: Kantor / Cabang"
                               class="mt-1 block w-full py-2 px-3 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('tipe_lokasi') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="radius" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Radius (Meter) <span class="text-rose-500">*</span></label>
                        <input type="number" id="radius" name="radius" value="{{ old('radius', 100) }}" required min="5" max="10000"
                               class="mt-1 block w-full py-2 px-3 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('radius') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="latitude" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Latitude <span class="text-rose-500">*</span></label>
                        <input type="text" id="latitude" name="latitude" value="{{ old('latitude', '-6.4025') }}" required
                               class="mt-1 block w-full py-2 px-3 text-sm font-mono border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('latitude') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="longitude" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Longitude <span class="text-rose-500">*</span></label>
                        <input type="text" id="longitude" name="longitude" value="{{ old('longitude', '106.7942') }}" required
                               class="mt-1 block w-full py-2 px-3 text-sm font-mono border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('longitude') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label for="zona_waktu" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Zona Waktu <span class="text-rose-500">*</span></label>
                        <select id="zona_waktu" name="zona_waktu" required
                                class="mt-1 block w-full py-2 px-3 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                            <option value="WIB" {{ old('zona_waktu', 'WIB') === 'WIB' ? 'selected' : '' }}>WIB (UTC+7)</option>
                            <option value="WITA" {{ old('zona_waktu') === 'WITA' ? 'selected' : '' }}>WITA (UTC+8)</option>
                            <option value="WIT" {{ old('zona_waktu') === 'WIT' ? 'selected' : '' }}>WIT (UTC+9)</option>
                        </select>
                        @error('zona_waktu') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="jam_masuk" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Jam Masuk <span class="text-rose-500">*</span></label>
                        <input type="time" id="jam_masuk" name="jam_masuk" value="{{ old('jam_masuk', '08:00') }}" required
                               class="mt-1 block w-full py-2 px-3 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('jam_masuk') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="jam_pulang" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Jam Pulang <span class="text-rose-500">*</span></label>
                        <input type="time" id="jam_pulang" name="jam_pulang" value="{{ old('jam_pulang', '17:00') }}" required
                               class="mt-1 block w-full py-2 px-3 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('jam_pulang') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Interactive Map Picker -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs p-6 flex flex-col space-y-3">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                    <h2 class="text-base font-semibold text-slate-900">Peta Penentuan Titik & Radius</h2>
                    <span class="text-xs text-slate-400">Klik peta untuk memindahkan titik</span>
                </div>
                <div id="map" class="w-full flex-1 min-h-[360px] rounded-lg border border-slate-200 z-10"></div>
                <p class="text-xs text-slate-500">Lingkaran biru merepresentasikan radius presensi yang diizinkan untuk staff.</p>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.lokasi.index') }}" class="px-4 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                Batal
            </a>
            <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow-xs transition-colors">
                Simpan Lokasi
            </button>
        </div>
    </form>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const latInput = document.getElementById('latitude');
            const lngInput = document.getElementById('longitude');
            const radiusInput = document.getElementById('radius');

            let lat = parseFloat(latInput.value) || -6.4025;
            let lng = parseFloat(lngInput.value) || 106.7942;
            let radius = parseInt(radiusInput.value) || 100;

            const map = L.map('map').setView([lat, lng], 16);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors',
                maxZoom: 19,
            }).addTo(map);

            let marker = L.marker([lat, lng], { draggable: true }).addTo(map);
            let circle = L.circle([lat, lng], {
                radius: radius,
                color: '#4f46e5',
                fillColor: '#818cf8',
                fillOpacity: 0.25
            }).addTo(map);

            function updatePosition(newLat, newLng) {
                latInput.value = newLat.toFixed(6);
                lngInput.value = newLng.toFixed(6);
                marker.setLatLng([newLat, newLng]);
                circle.setLatLng([newLat, newLng]);
            }

            marker.on('dragend', function(e) {
                const pos = e.target.getLatLng();
                updatePosition(pos.lat, pos.lng);
            });

            map.on('click', function(e) {
                updatePosition(e.latlng.lat, e.latlng.lng);
            });

            latInput.addEventListener('change', function() {
                const newLat = parseFloat(this.value);
                const currentLng = parseFloat(lngInput.value);
                if (!isNaN(newLat) && !isNaN(currentLng)) {
                    updatePosition(newLat, currentLng);
                    map.panTo([newLat, currentLng]);
                }
            });

            lngInput.addEventListener('change', function() {
                const currentLat = parseFloat(latInput.value);
                const newLng = parseFloat(this.value);
                if (!isNaN(currentLat) && !isNaN(newLng)) {
                    updatePosition(currentLat, newLng);
                    map.panTo([currentLat, newLng]);
                }
            });

            radiusInput.addEventListener('input', function() {
                const newRad = parseInt(this.value);
                if (!isNaN(newRad) && newRad >= 5) {
                    circle.setRadius(newRad);
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
