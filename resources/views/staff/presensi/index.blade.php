<x-app-layout title="Presensi Online GPS & Kamera">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Presensi Online</h1>
                <p class="text-sm text-slate-500 mt-1">
                    Validasi kehadiran menggunakan koordinat GPS dan bukti foto kamera langsung.
                </p>
            </div>

            <!-- Current Time Badge -->
            <div class="flex items-center gap-2 bg-white px-4 py-2 rounded-xl border border-slate-200 shadow-xs">
                <x-icon name="clock" class="w-4 h-4 text-indigo-600" />
                <span class="text-xs text-slate-500 font-medium">{{ \Carbon\Carbon::parse($today)->translatedFormat('d F Y') }} &bull;</span>
                <span class="text-xs font-bold text-slate-800 font-mono">{{ $nowInZone->format('H:i') }} {{ $lokasi?->zona_waktu ?? 'WIB' }}</span>
            </div>
        </div>
    </x-slot>

    @if(!$lokasi)
        <div class="p-6 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 flex items-start gap-4">
            <x-icon name="alert-triangle" class="w-6 h-6 text-amber-600 flex-shrink-0 mt-0.5" />
            <div>
                <h3 class="font-bold text-sm text-amber-900">Lokasi Presensi Belum Diatur</h3>
                <p class="text-xs text-amber-700 mt-1 leading-relaxed">
                    Data penugasan lokasi kantor belum dikonfigurasi untuk akun Anda. Silakan hubungi Administrator untuk mengatur lokasi kerja Anda.
                </p>
            </div>
        </div>
    @elseif($sudahKeluar)
        <div class="bg-white rounded-xl border border-slate-200 p-8 text-center max-w-lg mx-auto shadow-xs space-y-4">
            <div class="w-16 h-16 rounded-full bg-emerald-50 text-emerald-600 mx-auto flex items-center justify-center">
                <x-icon name="check-circle" class="w-8 h-8" />
            </div>
            <h2 class="text-lg font-bold text-slate-900">Presensi Hari Ini Lengkap</h2>
            <p class="text-xs text-slate-500 leading-relaxed">
                Anda telah menyelesaikan presensi masuk (pukul {{ substr($presensiHariIni->jam_masuk, 0, 5) }}) dan presensi keluar (pukul {{ substr($presensiHariIni->jam_keluar, 0, 5) }}). Terima kasih atas kehadiran Anda hari ini.
            </p>
            <div class="pt-4 flex items-center justify-center gap-3">
                <a href="{{ route('staff.dashboard') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition-colors">
                    Kembali ke Dashboard
                </a>
                <a href="{{ route('staff.riwayat.index') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg transition-colors">
                    Lihat Riwayat Presensi
                </a>
            </div>
        </div>
    @else
        <!-- Presensi Form Container with Alpine.js -->
        <div x-data="attendanceHandler({
            officeLat: {{ (float) $lokasi->latitude }},
            officeLng: {{ (float) $lokasi->longitude }},
            maxRadius: {{ (int) $lokasi->radius }},
            officeName: '{{ addslashes($lokasi->nama_lokasi) }}',
            tipe: '{{ !$sudahMasuk ? 'masuk' : 'keluar' }}',
            csrfToken: '{{ csrf_token() }}',
            validateUrl: '{{ route('staff.presensi.validate-location') }}',
            submitUrl: '{{ route('staff.presensi.submit') }}',
            redirectUrl: '{{ route('staff.dashboard') }}'
        })" x-init="init()" class="space-y-6">

            <!-- Presensi Mode Indicator Bar -->
            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg {{ !$sudahMasuk ? 'bg-indigo-50 text-indigo-600' : 'bg-emerald-50 text-emerald-600' }} flex items-center justify-center font-bold">
                        <x-icon name="camera" class="w-5 h-5" />
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-sm font-bold text-slate-900">
                                {{ !$sudahMasuk ? 'Presensi Masuk Kerja' : 'Presensi Keluar (Pulang)' }}
                            </h2>
                            <x-badge type="{{ !$sudahMasuk ? 'info' : 'success' }}" text="{{ !$sudahMasuk ? 'Check-In' : 'Check-Out' }}" />
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Target lokasi: <span class="font-semibold text-slate-700">{{ $lokasi->nama_lokasi }}</span> (Radius toleransi {{ $lokasi->radius }} meter)
                        </p>
                    </div>
                </div>

                <!-- GPS Status Badge -->
                <div class="flex items-center gap-3">
                    <div class="text-right">
                        <div class="text-[11px] text-slate-400 font-medium">Status Validasi Lokasi</div>
                        <div class="text-xs font-bold" :class="inRadius ? 'text-emerald-600' : (userLat ? 'text-rose-600' : 'text-slate-500')">
                            <span x-text="locationStatusText">Mendeteksi lokasi...</span>
                        </div>
                    </div>
                    <button type="button" 
                            @click="getLocation()" 
                            :disabled="loadingLocation"
                            class="p-2 text-slate-600 hover:text-indigo-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors" 
                        <span :class="loadingLocation ? 'animate-spin inline-block' : 'inline-block'">
                            <x-icon name="refresh" class="w-4 h-4" />
                        </span>
                    </button>
                </div>
            </div>

            <!-- Two-Column Layout: Location Map & Camera Stream -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- 1. GPS Location & Map Panel -->
                <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs flex flex-col justify-between space-y-4">
                    <div>
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-3">
                            <div class="flex items-center gap-2">
                                <x-icon name="map-pin" class="w-4 h-4 text-indigo-600" />
                                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">1. Posisi GPS & Radius</h3>
                            </div>
                            <span class="text-[11px] text-slate-400" x-text="distanceText"></span>
                        </div>

                        <!-- Leaflet Map Container -->
                        <div class="relative w-full h-64 rounded-xl overflow-hidden border border-slate-200 bg-slate-100">
                            <div id="map" class="w-full h-full z-10"></div>

                            <!-- Overlay when loading GPS -->
                            <div x-show="loadingLocation" class="absolute inset-0 bg-white/70 backdrop-blur-xs z-20 flex items-center justify-center gap-2 text-xs font-medium text-slate-600">
                                <x-icon name="refresh" class="w-4 h-4 animate-spin text-indigo-600" />
                                <span>Mengambil titik koordinat GPS...</span>
                            </div>
                        </div>

                        <!-- Coordinates & Distance Details -->
                        <div class="mt-4 grid grid-cols-2 gap-3 text-xs">
                            <div class="p-3 bg-slate-50 rounded-lg border border-slate-100">
                                <span class="text-slate-400 block text-[10px] uppercase font-semibold">Koordinat Anda</span>
                                <span class="font-mono font-semibold text-slate-800" x-text="userLat ? userLat.toFixed(5) + ', ' + userLng.toFixed(5) : 'Belum terdeteksi'"></span>
                            </div>
                            <div class="p-3 bg-slate-50 rounded-lg border border-slate-100">
                                <span class="text-slate-400 block text-[10px] uppercase font-semibold">Jarak ke Kantor</span>
                                <span class="font-mono font-bold" :class="inRadius ? 'text-emerald-700' : 'text-rose-700'" x-text="distance !== null ? distance + ' Meter' : '-'"></span>
                            </div>
                        </div>

                        <!-- GPS Error Notice if any -->
                        <div x-show="locationError" class="mt-3 p-3 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-xs flex items-start gap-2">
                            <x-icon name="alert-circle" class="w-4 h-4 flex-shrink-0 mt-0.5" />
                            <div x-text="locationError"></div>
                        </div>

                        <!-- Out of Radius Warning Banner -->
                        <div x-show="userLat && !inRadius && !loadingLocation" class="mt-3 p-3 rounded-lg bg-amber-50 border border-amber-200 text-amber-800 text-xs flex items-start gap-2">
                            <x-icon name="alert-triangle" class="w-4 h-4 flex-shrink-0 mt-0.5 text-amber-600" />
                            <div>
                                <span class="font-bold">Di Luar Radius Presensi!</span>
                                <p class="mt-0.5 text-amber-700 leading-relaxed">
                                    Jarak Anda saat ini melebihi toleransi maksimal radius kantor (<span x-text="maxRadius"></span> meter). Presensi hanya dapat dikirim jika Anda berada di dalam radius.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Quick simulation button for localhost / demo testing if GPS outside office -->
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                        <span>Pastikan izin lokasi pada browser diizinkan.</span>
                        <button type="button" @click="simulateOfficeLocation()" class="text-indigo-600 hover:text-indigo-800 font-semibold underline">
                            Gunakan Titik Kantor (Simulasi Uji)
                        </button>
                    </div>
                </div>

                <!-- 2. Camera Snapshot Panel -->
                <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs flex flex-col justify-between space-y-4">
                    <div>
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-3">
                            <div class="flex items-center gap-2">
                                <x-icon name="camera" class="w-4 h-4 text-indigo-600" />
                                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">2. Kamera Wajah (Live Snapshot)</h3>
                            </div>
                            <span class="text-[11px] font-medium" :class="capturedPhoto ? 'text-emerald-600' : 'text-slate-400'">
                                <span x-text="capturedPhoto ? 'Foto siap dikirim' : 'Kamera siap'"></span>
                            </span>
                        </div>

                        <!-- Video / Snapshot Preview Canvas -->
                        <div class="relative w-full h-64 rounded-xl overflow-hidden border border-slate-200 bg-slate-900 flex items-center justify-center">
                            <!-- Live Camera Video -->
                            <video id="webcam" 
                                   x-show="!capturedPhoto" 
                                   autoplay 
                                   playsinline 
                                   muted
                                   class="w-full h-full object-cover"></video>

                            <!-- Hidden Canvas for snapshot drawing -->
                            <canvas id="snapshotCanvas" class="hidden"></canvas>

                            <!-- Captured Snapshot Preview -->
                            <template x-if="capturedPhoto">
                                <img :src="capturedPhoto" alt="Snapshot Foto Presensi" class="w-full h-full object-cover">
                            </template>

                            <!-- Camera Error / Initializing message -->
                            <div x-show="cameraError" class="absolute inset-0 bg-slate-900/80 p-6 flex flex-col items-center justify-center text-center text-white space-y-2">
                                <x-icon name="alert-circle" class="w-8 h-8 text-rose-400" />
                                <span class="text-xs font-medium text-rose-200" x-text="cameraError"></span>
                                <button type="button" @click="initCamera()" class="mt-2 px-3 py-1.5 bg-white/20 hover:bg-white/30 text-white rounded text-xs font-medium">
                                    Coba Lagi
                                </button>
                            </div>

                            <!-- Camera Face Guide Overlay -->
                            <div x-show="!capturedPhoto && !cameraError" class="pointer-events-none absolute inset-0 flex items-center justify-center">
                                <div class="w-40 h-48 border-2 border-dashed border-white/60 rounded-full"></div>
                            </div>
                        </div>

                        <!-- Camera Action Controls -->
                        <div class="mt-4 flex items-center gap-3">
                            <template x-if="!capturedPhoto">
                                <button type="button" 
                                        @click="takeSnapshot()" 
                                        :disabled="cameraError"
                                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold rounded-lg shadow-xs transition-colors">
                                    <x-icon name="camera" class="w-4 h-4" />
                                    <span>Ambil Foto Bukti Kehadiran</span>
                                </button>
                            </template>
                            <template x-if="capturedPhoto">
                                <div class="flex items-center gap-3 w-full">
                                    <button type="button" 
                                            @click="retakePhoto()" 
                                            class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition-colors">
                                        <x-icon name="refresh" class="w-4 h-4" />
                                        <span>Ambil Ulang Foto</span>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 text-[11px] text-slate-400">
                        Foto diambil secara langsung dari kamera web/perangkat tanpa upload gambar manual.
                    </div>
                </div>

            </div>

            <!-- Submit Attendance Final Button Panel -->
            <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="text-sm font-bold text-slate-900">Konfirmasi & Kirim Presensi</div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Pastikan koordinat berada di dalam radius kantor dan foto wajah sudah diambil dengan jelas.
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <button type="button" 
                            @click="submitAttendance()" 
                            :disabled="!canSubmit || submitting"
                            class="inline-flex items-center justify-center gap-2 px-6 py-3 text-xs font-bold rounded-lg shadow-xs transition-all"
                            :class="canSubmit && !submitting 
                                ? 'bg-indigo-600 hover:bg-indigo-700 text-white cursor-pointer shadow-indigo-100' 
                                : 'bg-slate-200 text-slate-400 cursor-not-allowed'">
                        <x-icon name="check-circle" class="w-4 h-4" x-show="!submitting" />
                        <x-icon name="refresh" class="w-4 h-4 animate-spin" x-show="submitting" />
                        <span x-text="submitButtonText">Kirim Presensi</span>
                    </button>
                </div>
            </div>

            <!-- Response Alert Modal -->
            <div x-show="resultModal" 
                 x-cloak 
                 class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
                <div class="bg-white rounded-xl max-w-sm w-full p-6 shadow-xl border border-slate-200 text-center space-y-4">
                    <div class="w-14 h-14 rounded-full mx-auto flex items-center justify-center"
                         :class="resultSuccess ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600'">
                        <template x-if="resultSuccess">
                            <x-icon name="check-circle" class="w-8 h-8" />
                        </template>
                        <template x-if="!resultSuccess">
                            <x-icon name="x-circle" class="w-8 h-8" />
                        </template>
                    </div>

                    <h3 class="text-base font-bold text-slate-900" x-text="resultSuccess ? 'Presensi Berhasil' : 'Presensi Gagal'"></h3>
                    <p class="text-xs text-slate-500 leading-relaxed" x-text="resultMessage"></p>

                    <div class="pt-2">
                        <template x-if="resultSuccess">
                            <a :href="redirectUrl" class="w-full inline-flex justify-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg transition-colors">
                                Kembali ke Dashboard
                            </a>
                        </template>
                        <template x-if="!resultSuccess">
                            <button type="button" @click="resultModal = false" class="w-full px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition-colors">
                                Tutup & Coba Lagi
                            </button>
                        </template>
                    </div>
                </div>
            </div>

        </div>
    @endif

    @push('styles')
    <style>
        .leaflet-container {
            font-family: inherit;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        function attendanceHandler(config) {
            return {
                officeLat: config.officeLat,
                officeLng: config.officeLng,
                maxRadius: config.maxRadius,
                officeName: config.officeName,
                tipe: config.tipe,
                csrfToken: config.csrfToken,
                validateUrl: config.validateUrl,
                submitUrl: config.submitUrl,
                redirectUrl: config.redirectUrl,

                map: null,
                officeMarker: null,
                userMarker: null,
                radiusCircle: null,

                userLat: null,
                userLng: null,
                distance: null,
                inRadius: false,
                loadingLocation: false,
                locationError: null,

                videoStream: null,
                cameraActive: false,
                cameraError: null,
                capturedPhoto: null,
                submitting: false,

                resultModal: false,
                resultSuccess: false,
                resultMessage: '',

                get locationStatusText() {
                    if (this.loadingLocation) return 'Mencari sinyal GPS...';
                    if (!this.userLat) return 'Menunggu izin GPS';
                    if (this.inRadius) return 'Dalam Radius (' + this.distance + ' m)';
                    return 'Di Luar Radius (' + this.distance + ' m)';
                },

                get distanceText() {
                    if (this.distance === null) return 'Maks. ' + this.maxRadius + ' m';
                    return this.distance + ' m dari kantor (Maks: ' + this.maxRadius + ' m)';
                },

                get canSubmit() {
                    return this.userLat !== null && this.inRadius === true && this.capturedPhoto !== null;
                },

                get submitButtonText() {
                    if (this.submitting) return 'Memproses Presensi...';
                    if (!this.userLat) return 'Menunggu Lokasi GPS';
                    if (!this.inRadius) return 'Di Luar Radius Kantor';
                    if (!this.capturedPhoto) return 'Ambil Foto Terlebih Dahulu';
                    return this.tipe === 'masuk' ? 'Kirim Presensi Masuk' : 'Kirim Presensi Keluar';
                },

                init() {
                    this.$nextTick(() => {
                        this.initMap();
                        this.getLocation();
                        this.initCamera();
                    });
                },

                initMap() {
                    const container = document.getElementById('map');
                    if (!container) return;

                    this.map = L.map('map').setView([this.officeLat, this.officeLng], 16);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; OpenStreetMap'
                    }).addTo(this.map);

                    // Office Marker
                    this.officeMarker = L.marker([this.officeLat, this.officeLng])
                        .addTo(this.map)
                        .bindPopup('<b>' + this.officeName + '</b><br>Lokasi Presensi Resmi')
                        .openPopup();

                    // Office Radius Circle
                    this.radiusCircle = L.circle([this.officeLat, this.officeLng], {
                        color: '#4f46e5',
                        fillColor: '#6366f1',
                        fillOpacity: 0.15,
                        radius: this.maxRadius
                    }).addTo(this.map);
                },

                getLocation() {
                    if (!navigator.geolocation) {
                        this.locationError = 'Browser Anda tidak mendukung deteksi lokasi Geolocation.';
                        return;
                    }

                    this.loadingLocation = true;
                    this.locationError = null;

                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            this.userLat = position.coords.latitude;
                            this.userLng = position.coords.longitude;
                            this.loadingLocation = false;

                            this.updateUserMarker();
                            this.validateWithServer();
                        },
                        (error) => {
                            this.loadingLocation = false;
                            switch(error.code) {
                                case error.PERMISSION_DENIED:
                                    this.locationError = 'Akses lokasi ditolak oleh pengguna. Silakan aktifkan izin lokasi.';
                                    break;
                                case error.POSITION_UNAVAILABLE:
                                    this.locationError = 'Informasi lokasi tidak tersedia pada perangkat.';
                                    break;
                                case error.TIMEOUT:
                                    this.locationError = 'Waktu permintaan lokasi GPS habis. Silakan coba lagi.';
                                    break;
                                default:
                                    this.locationError = 'Gagal mendeteksi lokasi GPS.';
                            }
                        },
                        { enableHighAccuracy: true, timeout: 12000, maximumAge: 0 }
                    );
                },

                simulateOfficeLocation() {
                    // Small perturbation inside the office radius for testing
                    this.userLat = this.officeLat + 0.00005;
                    this.userLng = this.officeLng + 0.00005;
                    this.locationError = null;
                    this.updateUserMarker();
                    this.validateWithServer();
                },

                updateUserMarker() {
                    if (!this.map) return;

                    if (this.userMarker) {
                        this.userMarker.setLatLng([this.userLat, this.userLng]);
                    } else {
                        const userIcon = L.divIcon({
                            className: 'custom-user-marker',
                            html: '<div style="background-color:#2563eb;width:14px;height:14px;border-radius:50%;border:3px solid #ffffff;box-shadow:0 0 8px rgba(37,99,235,0.8);"></div>',
                            iconSize: [14, 14],
                            iconAnchor: [7, 7]
                        });

                        this.userMarker = L.marker([this.userLat, this.userLng], { icon: userIcon })
                            .addTo(this.map)
                            .bindPopup('Posisi Anda Saat Ini');
                    }

                    const group = L.featureGroup([this.officeMarker, this.userMarker, this.radiusCircle]);
                    this.map.fitBounds(group.getBounds().pad(0.2));
                },

                async validateWithServer() {
                    try {
                        const response = await fetch(this.validateUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                latitude: this.userLat,
                                longitude: this.userLng
                            })
                        });

                        const data = await response.json();
                        if (data.success) {
                            this.inRadius = data.in_radius;
                            this.distance = data.distance;
                        }
                    } catch (e) {
                        console.error('Validation error', e);
                    }
                },

                async initCamera() {
                    this.cameraError = null;
                    const video = document.getElementById('webcam');

                    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                        this.cameraError = 'Browser tidak mendukung akses kamera langsung.';
                        return;
                    }

                    try {
                        this.videoStream = await navigator.mediaDevices.getUserMedia({
                            video: {
                                facingMode: 'user',
                                width: { ideal: 640 },
                                height: { ideal: 480 }
                            },
                            audio: false
                        });

                        if (video) {
                            video.srcObject = this.videoStream;
                            this.cameraActive = true;
                        }
                    } catch (err) {
                        this.cameraError = 'Akses kamera tidak diizinkan atau kamera tidak tersedia.';
                        console.error(err);
                    }
                },

                takeSnapshot() {
                    const video = document.getElementById('webcam');
                    const canvas = document.getElementById('snapshotCanvas');

                    if (!video || !canvas) return;

                    const width = video.videoWidth || 640;
                    const height = video.videoHeight || 480;

                    canvas.width = width;
                    canvas.height = height;

                    const context = canvas.getContext('2d');
                    context.drawImage(video, 0, 0, width, height);

                    this.capturedPhoto = canvas.toDataURL('image/jpeg', 0.85);
                },

                retakePhoto() {
                    this.capturedPhoto = null;
                },

                async submitAttendance() {
                    if (!this.canSubmit || this.submitting) return;

                    this.submitting = true;

                    try {
                        const response = await fetch(this.submitUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                tipe: this.tipe,
                                latitude: this.userLat,
                                longitude: this.userLng,
                                foto: this.capturedPhoto
                            })
                        });

                        const res = await response.json();

                        this.submitting = false;
                        this.resultSuccess = res.success === true;
                        this.resultMessage = res.message;
                        this.resultModal = true;

                        if (this.resultSuccess && this.videoStream) {
                            this.videoStream.getTracks().forEach(track => track.stop());
                        }
                    } catch (err) {
                        this.submitting = false;
                        this.resultSuccess = false;
                        this.resultMessage = 'Terjadi kesalahan sistem saat mengirim data presensi.';
                        this.resultModal = true;
                    }
                }
            };
        }
    </script>
    @endpush
</x-app-layout>
