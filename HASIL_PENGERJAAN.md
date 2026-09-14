# Laporan Hasil Pengerjaan Aplikasi Web Presensi Online (Presensi)

Dokumen ini berisi dokumentasi lengkap hasil pengerjaan, arsitektur sistem, fitur-fitur yang dibangun, konfigurasi data, dan panduan penggunaan aplikasi web presensi online berbasis lokasi (GPS) dan kamera (**Presensi**) sesuai dengan spesifikasi dokumen `AGENTS.md`.

---

## 1. Ringkasan Proyek

| Informasi | Deskripsi |
| :--- | :--- |
| **Nama Aplikasi** | **Presensi** |
| **Fungsi Utama** | Pencatatan kehadiran pegawai/guru secara digital dengan validasi radius lokasi GPS, bukti foto kamera real-time, serta pengajuan izin/cuti/sakit. |
| **Framework Backend** | Laravel 12.x (PHP 8.2+) |
| **Database** | MySQL / MariaDB (nama database: `presensi`) |
| **Styling & Frontend** | Tailwind CSS v4, Blade Template, Alpine.js |
| **Peta Interaktif** | Leaflet.js & OpenStreetMap |
| **Lokasi Utama Terdaftar** | **SMK Tunas Media** (Jl. Raya Cinangka No. 88, Kedaung, Kec. Sawangan, Kota Depok) |

---

## 2. Struktur Database & Model

Skema tabel dibuat menggunakan Laravel Migration resmi dengan relasi foreign key dan cascading:

1. **`jabatan`**:
   - `id` (PK, Auto Increment)
   - `jabatan` (VARCHAR)
   - *Model:* `App\Models\Jabatan`

2. **`lokasi_presensi`**:
   - `id` (PK, Auto Increment)
   - `nama_lokasi` (VARCHAR, e.g. "SMK Tunas Media")
   - `alamat_lokasi` (VARCHAR)
   - `tipe_lokasi` (VARCHAR, e.g. "Sekolah", "Kantor")
   - `latitude` & `longitude` (VARCHAR)
   - `radius` (INT, dalam satuan meter)
   - `zona_waktu` (VARCHAR, e.g. "WIB", "WITA", "WIT")
   - `jam_masuk` & `jam_pulang` (TIME)
   - *Model:* `App\Models\LokasiPresensi`

3. **`pegawai`**:
   - `id` (PK, Auto Increment)
   - `nrg` (VARCHAR, Unique)
   - `nama` (VARCHAR)
   - `jenis_kelamin` (VARCHAR)
   - `alamat` (VARCHAR)
   - `no_handphone` (VARCHAR)
   - `jabatan` (VARCHAR)
   - `lokasi_presensi` (VARCHAR)
   - `foto` (VARCHAR, default `default.png`)
   - *Model:* `App\Models\Pegawai`

4. **`users`**:
   - `id` (PK, Auto Increment)
   - `id_pegawai` (FK -> `pegawai.id`, Cascade on Delete)
   - `username` (VARCHAR, Unique)
   - `password` (VARCHAR, Bcrypt Hash)
   - `status` (VARCHAR: `aktif` / `nonaktif`)
   - `role` (VARCHAR: `admin` / `staff`)
   - *Model:* `App\Models\User`

5. **`presensi`**:
   - `id` (PK, Auto Increment)
   - `id_pegawai` (FK -> `pegawai.id`, Cascade on Delete)
   - `tanggal_masuk` (DATE)
   - `jam_masuk` (TIME)
   - `foto_masuk` (VARCHAR)
   - `tanggal_keluar` (DATE, Nullable)
   - `jam_keluar` (TIME, Nullable)
   - `foto_keluar` (VARCHAR, Nullable)
   - *Model:* `App\Models\Presensi`

6. **`ketidakhadiran`**:
   - `id` (PK, Auto Increment)
   - `id_pegawai` (FK -> `pegawai.id`, Cascade on Delete)
   - `keterangan` (VARCHAR: Sakit, Cuti, Dinas Luar, dll)
   - `tanggal` (DATE)
   - `deskripsi` (VARCHAR, Nullable)
   - `file` (VARCHAR, Nullable lampiran surat/dokumen)
   - `status_pengajuan` (VARCHAR: `menunggu`, `disetujui`, `ditolak`)
   - *Model:* `App\Models\Ketidakhadiran`

---

## 3. Fitur yang Telah Diimplementasikan

### A. Role Administrator
1. **Dashboard Admin (`/admin/dashboard`)**:
   - Ringkasan statistik kehadiran harian (Jumlah Pegawai, Hadir Hari Ini, Terlambat, Izin Menunggu).
   - Aktivitas presensi live hari ini dengan foto thumbnail.
   - Panel permohonan izin terbaru yang membutuhkan persetujuan cepat.
2. **Kelola Data Pegawai (`/admin/pegawai`)**:
   - CRUD lengkap: Tambah pegawai baru (otomatis membuat akun login user), edit data, detail profil, dan hapus pegawai.
3. **Kelola Data Jabatan (`/admin/jabatan`)**:
   - CRUD master data jabatan dengan modal interaktif Alpine.js.
4. **Kelola Data Lokasi Presensi (`/admin/lokasi`)**:
   - CRUD titik kantor/sekolah, alamat, koordinat latitude/longitude, toleransi radius meter, jam kerja masuk/pulang, dan zona waktu.
5. **Rekap Presensi & Cetak (`/admin/rekap`)**:
   - Filter rekapitulasi kehadiran berdasarkan rentang tanggal, lokasi penugasan, dan pegawai.
   - Halaman cetak laporan resmi dengan kop surat standar cetak (`/admin/rekap/print`).
6. **Persetujuan Ketidakhadiran (`/admin/ketidakhadiran`)**:
   - Verifikasi permohonan izin/cuti/sakit staff.
   - Tab filter status (Menunggu, Disetujui, Ditolak, Semua) dan tombol aksi Setujui / Tolak.
7. **Manajemen Akun Pengguna (`/admin/users`)**:
   - Pengaturan role akun (`admin` / `staff`), status (`aktif` / `nonaktif`), dan reset password akun.
8. **Profil Administrator (`/admin/profile`)**:
   - Pembaruan nama, nomor WhatsApp/telepon, alamat, foto avatar profil, serta ganti kata sandi.

---

### B. Role Staff / Pegawai
1. **Dashboard Staff (`/staff/dashboard`)**:
   - Status presensi hari ini (Jam masuk, jam pulang, dan status ketepatan waktu).
   - Widget jam digital real-time sesuai zona waktu penugasan (WIB/WITA/WIT).
   - Kartu informasi lokasi kantor penugasan (nama kantor, alamat, jam operasional, radius toleransi).
   - Statistik kehadiran bulanan (Total Hadir, Terlambat, Izin Disetujui).
   - Tabel riwayat 5 presensi terakhir dengan modal penampil bukti foto.
2. **Presensi Online GPS & Kamera (`/staff/presensi`)**:
   - Deteksi koordinat GPS perangkat otomatis via HTML5 Geolocation API.
   - Peta Leaflet.js menampilkan titik lokasi resmi, batas lingkaran radius, dan posisi staff saat ini.
   - Validasi ganda (frontend dan backend) menggunakan *Haversine Formula*.
   - Kamera wajah langsung (`MediaDevices.getUserMedia`) dengan capture ke Canvas (Base64) tanpa upload foto lama.
   - Peringatan jelas bila berada di luar radius lokasi resmi.
   - Tombol simulasi titik kantor untuk pengujian di komputer/localhost.
3. **Riwayat Presensi Pribadi (`/staff/riwayat`)**:
   - Filter pencarian per bulan dan tahun.
   - Tabel lengkap catatan jam masuk, foto bukti masuk, jam keluar, foto bukti keluar, dan status keterlambatan (Tepat Waktu / Terlambat).
   - Modal penampil foto presensi resolusi penuh.
4. **Pengajuan Izin / Ketidakhadiran (`/staff/ketidakhadiran/create`)**:
   - Formulir pengajuan izin baru (Sakit, Cuti, Dinas Luar, Keperluan Keluarga, Lainnya).
   - Pilihan tanggal, deskripsi keterangan, dan unggah berkas bukti (PDF / JPG / PNG).
5. **Status Pengajuan Izin (`/staff/ketidakhadiran`)**:
   - Daftar riwayat pengajuan izin lengkap dengan badge status (`Menunggu`, `Disetujui`, `Ditolak`) dan tombol unduh berkas bukti.
6. **Profil Staff (`/staff/profile`)**:
   - Ringkasan identitas pegawai (NRG, Nama, Jabatan, Lokasi Kerja).
   - Pembaruan kontak, domisili, foto profil, dan ganti kata sandi.

---

## 4. Ketentuan Desain UI/UX (Sesuai AGENTS.md)

1. **Bebas Emoji**: Seluruh antarmuka, tombol, label status, dan pesan notifikasi **tidak menggunakan emoji/emoticon**. Semua representasi visual murni menggunakan ikon SVG outline pada komponen `<x-icon>`.
2. **Palet Warna Minimalis**: Skema netral (*slate-50*, *slate-100*, *slate-200*, *slate-800*, *slate-900*) dengan warna aksen seragam (*indigo-600* dan *emerald-600*).
3. **Responsif Mobile**: Tampilan dioptimalkan untuk perangkat ponsel cerdas (*smartphone*) dan desktop.

---

## 5. Lokasi Kantor Utama (SMK Tunas Media)

Data lokasi kantor utama telah disesuaikan ke **SMK Tunas Media Depok**:
- **Nama Lokasi**: `SMK Tunas Media`
- **Alamat**: `Jl. Raya Cinangka No. 88, Kedaung, Kec. Sawangan, Kota Depok, Jawa Barat`
- **Koordinat Latitude**: `-6.370835`
- **Koordinat Longitude**: `106.746056`
- **Radius Presensi**: `100 Meter`
- **Jam Kerja**: `07:00:00 - 15:30:00 WIB`

---

## 6. Akun Login Default untuk Pengujian

Semua akun menggunakan kata sandi standar `password123`:

| Role | Username | Password | Penugasan Lokasi | Keterangan |
| :--- | :--- | :--- | :--- | :--- |
| **Admin** | `admin` | `password123` | SMK Tunas Media | Administrator Sistem |
| **Staff** | `siti.aminah` | `password123` | SMK Tunas Media | Staff Operasional |
| **Staff** | `andi.wijaya` | `password123` | Kantor Cabang | Staff Keuangan |

---

## 7. Cara Menjalankan Aplikasi Secara Lokal

### Prasyarat
- PHP 8.2 atau lebih baru dengan ekstensi `pdo_mysql`, `mbstring`, `gd`, `fileinfo`.
- Node.js 18+ dan npm.
- MySQL / MariaDB Server aktif.

### Langkah-Langkah:
1. **Konfigurasi Database `.env`**:
   Pastikan konfigurasi database di file `.env` sudah sesuai:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=presensi
   DB_USERNAME=root
   DB_PASSWORD=
   ```

2. **Jalankan Migrasi & Seeder (Opsional jika ingin reset data)**:
   ```bash
   php artisan migrate:fresh --seed
   ```

3. **Buat Tautan Storage**:
   ```bash
   php artisan storage:link
   ```

4. **Kompilasi Aset Frontend (Tailwind CSS & Vite)**:
   ```bash
   npm run build
   # atau untuk mode dev watch:
   # npm run dev
   ```

5. **Jalankan Server Lokal**:
   ```bash
   php artisan serve --port=8000
   ```
   Aplikasi dapat diakses melalui browser di alamat: `http://127.0.0.1:8000`.

---

## 8. Ringkasan Pengujian Otomatis (Testing)

Pengujian fitur otomatis dibuat pada `tests/Feature/PresensiFullFlowTest.php` dan telah diuji dengan PHPUnit:

```text
   PASS  Tests\Unit\ExampleTest
  ✓ that true is true

   PASS  Tests\Feature\ExampleTest
  ✓ the application returns a successful response

   PASS  Tests\Feature\PresensiFullFlowTest
  ✓ guest is redirected to login
  ✓ staff cannot access admin pages
  ✓ admin cannot access staff pages
  ✓ staff dashboard loads correctly
  ✓ staff presensi page loads
  ✓ gps location validation api
  ✓ staff presensi submission validation
  ✓ staff riwayat presensi page
  ✓ staff can submit leave request
  ✓ staff profile page and update
  ✓ admin profile page and update
  ✓ admin can approve staff leave request

  Tests:    14 passed (51 assertions)
  Status:   100% SUCCESS
```
