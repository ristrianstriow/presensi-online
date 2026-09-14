# Prompt: Pengembangan Aplikasi Presensi Online Berbasis Lokasi (GPS)

## 1. Konteks & Tujuan

Buatkan aplikasi web presensi/absensi online berbasis lokasi (GPS) bernama **Presensi2**. Aplikasi ini digunakan oleh instansi/perusahaan untuk mencatat kehadiran pegawai secara digital, lengkap dengan validasi lokasi, bukti foto, serta pengajuan izin/ketidakhadiran. Aplikasi memiliki dua role pengguna: **Admin** dan **Staff**.

Database sudah dirancang sebelumnya (nama database: `presensi`) dan harus diikuti sesuai skema pada bagian 3. Gunakan skema ini sebagai acuan utama, boleh ditambahkan foreign key/relasi yang belum eksplisit digambarkan agar data tetap konsisten.

---

## 2. Role & Hak Akses

### Admin
- Login ke dashboard admin.
- Kelola data pegawai (tambah, ubah, hapus, lihat detail).
- Kelola data jabatan.
- Kelola data lokasi presensi (nama lokasi, koordinat, radius, zona waktu, jam masuk/pulang).
- Melihat rekap presensi seluruh pegawai (jam masuk, jam keluar, foto, status).
- Menyetujui atau menolak pengajuan izin/ketidakhadiran staff.
- Melihat laporan/statistik kehadiran (harian, mingguan, bulanan).
- Kelola akun user (username, status aktif/nonaktif, role).

### Staff
- Login ke dashboard staff.
- Melakukan presensi masuk & keluar dengan validasi lokasi GPS (harus berada dalam radius lokasi presensi yang ditentukan) dan mengambil foto sebagai bukti.
- Melihat riwayat presensi pribadi (tanggal, jam masuk, jam keluar, status).
- Mengajukan izin/ketidakhadiran (sakit, cuti, dinas luar, dll) dengan keterangan, deskripsi, dan lampiran file.
- Melihat status pengajuan izin (menunggu, disetujui, ditolak).
- Melihat/mengubah profil pribadi.

---

## 3. Skema Database (SQL)

Gunakan nama database `presensi`. Buat struktur tabel berikut lengkap dengan relasi foreign key:

```sql
CREATE DATABASE IF NOT EXISTS presensi;
USE presensi;

-- Tabel jabatan
CREATE TABLE jabatan (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    jabatan VARCHAR(50) NOT NULL
);

-- Tabel lokasi_presensi
CREATE TABLE lokasi_presensi (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    nama_lokasi VARCHAR(50) NOT NULL,
    alamat_lokasi VARCHAR(225) NOT NULL,
    tipe_lokasi VARCHAR(50) NOT NULL,
    latitude VARCHAR(50) NOT NULL,
    longitude VARCHAR(50) NOT NULL,
    radius INT(11) NOT NULL,
    zona_waktu VARCHAR(4) NOT NULL,
    jam_masuk TIME NOT NULL,
    jam_pulang TIME NOT NULL
);

-- Tabel pegawai
CREATE TABLE pegawai (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    nrg VARCHAR(50) NOT NULL UNIQUE,
    nama VARCHAR(50) NOT NULL,
    jenis_kelamin VARCHAR(10) NOT NULL,
    alamat VARCHAR(225) NOT NULL,
    no_handphone VARCHAR(20) NOT NULL,
    jabatan VARCHAR(50) NOT NULL,
    lokasi_presensi VARCHAR(50) NOT NULL,
    foto VARCHAR(225) DEFAULT NULL
);

-- Tabel users (akun login, terhubung ke pegawai)
CREATE TABLE users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    id_pegawai INT(11) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(225) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'aktif',
    role VARCHAR(20) NOT NULL,
    FOREIGN KEY (id_pegawai) REFERENCES pegawai(id) ON DELETE CASCADE
);

-- Tabel presensi (catatan jam masuk & keluar harian)
CREATE TABLE presensi (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    id_pegawai INT(11) NOT NULL,
    tanggal_masuk DATE NOT NULL,
    jam_masuk TIME NOT NULL,
    foto_masuk VARCHAR(225) NOT NULL,
    tanggal_keluar DATE DEFAULT NULL,
    jam_keluar TIME DEFAULT NULL,
    foto_keluar VARCHAR(225) DEFAULT NULL,
    FOREIGN KEY (id_pegawai) REFERENCES pegawai(id) ON DELETE CASCADE
);

-- Tabel ketidakhadiran (pengajuan izin/cuti/sakit)
CREATE TABLE ketidakhadiran (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    id_pegawai INT(11) NOT NULL,
    keterangan VARCHAR(50) NOT NULL,
    tanggal DATE NOT NULL,
    deskripsi VARCHAR(225) DEFAULT NULL,
    file VARCHAR(225) DEFAULT NULL,
    status_pengajuan VARCHAR(20) NOT NULL DEFAULT 'menunggu',
    FOREIGN KEY (id_pegawai) REFERENCES pegawai(id) ON DELETE CASCADE
);
```

---

## 4. Dummy Data (Seeder SQL)

Sertakan data contoh berikut agar aplikasi bisa langsung diuji, mencakup role **admin** dan **staff**:

```sql
-- Data jabatan
INSERT INTO jabatan (id, jabatan) VALUES
(1, 'Administrator'),
(2, 'Staff Operasional'),
(3, 'Staff Keuangan');

-- Data lokasi presensi
INSERT INTO lokasi_presensi (id, nama_lokasi, alamat_lokasi, tipe_lokasi, latitude, longitude, radius, zona_waktu, jam_masuk, jam_pulang) VALUES
(1, 'Kantor Pusat', 'Jl. Merdeka No. 10, Depok', 'Kantor', '-6.4025', '106.7942', 100, 'WIB', '08:00:00', '17:00:00'),
(2, 'Kantor Cabang', 'Jl. Sudirman No. 5, Jakarta', 'Kantor', '-6.2088', '106.8456', 150, 'WIB', '08:30:00', '17:30:00');

-- Data pegawai
INSERT INTO pegawai (id, nrg, nama, jenis_kelamin, alamat, no_handphone, jabatan, lokasi_presensi, foto) VALUES
(1, 'NRG001', 'Budi Santoso', 'Laki-laki', 'Jl. Kenanga No. 1, Depok', '081234567890', 'Administrator', 'Kantor Pusat', 'default.png'),
(2, 'NRG002', 'Siti Aminah', 'Perempuan', 'Jl. Mawar No. 2, Depok', '081234567891', 'Staff Operasional', 'Kantor Pusat', 'default.png'),
(3, 'NRG003', 'Andi Wijaya', 'Laki-laki', 'Jl. Melati No. 3, Jakarta', '081234567892', 'Staff Keuangan', 'Kantor Cabang', 'default.png');

-- Data users (password contoh sudah di-hash, gunakan bcrypt pada implementasi asli)
INSERT INTO users (id, id_pegawai, username, password, status, role) VALUES
(1, 1, 'admin', '$2y$10$hashedpasswordadmin', 'aktif', 'admin'),
(2, 2, 'siti.aminah', '$2y$10$hashedpasswordstaff1', 'aktif', 'staff'),
(3, 3, 'andi.wijaya', '$2y$10$hashedpasswordstaff2', 'aktif', 'staff');

-- Data presensi
INSERT INTO presensi (id, id_pegawai, tanggal_masuk, jam_masuk, foto_masuk, tanggal_keluar, jam_keluar, foto_keluar) VALUES
(1, 2, '2026-09-08', '07:55:00', 'masuk_siti_20260908.jpg', '2026-09-08', '17:05:00', 'keluar_siti_20260908.jpg'),
(2, 3, '2026-09-08', '08:20:00', 'masuk_andi_20260908.jpg', NULL, NULL, NULL);

-- Data ketidakhadiran
INSERT INTO ketidakhadiran (id, id_pegawai, keterangan, tanggal, deskripsi, file, status_pengajuan) VALUES
(1, 2, 'Sakit', '2026-09-05', 'Demam tinggi, disertai surat dokter', 'surat_dokter_siti.pdf', 'disetujui'),
(2, 3, 'Cuti', '2026-09-10', 'Cuti tahunan keperluan keluarga', NULL, 'menunggu');
```

---

## 5. Ketentuan Desain UI/UX

- **Styling utama menggunakan Tailwind CSS.** Seluruh komponen UI dibangun dengan utility class Tailwind, hindari CSS custom kecuali benar-benar diperlukan.
- **Palet warna minimalis**, hindari terlalu banyak variasi warna. Gunakan skema netral (putih, abu-abu terang, abu-abu gelap/hitam untuk teks) dengan **satu warna aksen** untuk elemen penting (tombol utama, status aktif, indikator).
- **Tidak menggunakan emoji atau emoticon** di seluruh antarmuka maupun teks sistem. Jika perlu penanda visual, gunakan **icon** dari library ikon (misalnya Lucide Icons atau Heroicons) dengan gaya outline/line yang konsisten.
- **Layout terstruktur dan rapi**:
  - Gunakan sidebar navigasi tetap untuk admin dan staff (menu berbeda sesuai role).
  - Gunakan topbar berisi nama pengguna, role, dan tombol logout.
  - Konten utama ditampilkan dalam card/panel dengan padding dan spacing konsisten.
  - Tabel data (pegawai, presensi, ketidakhadiran) menggunakan style tabel bersih: garis pemisah tipis, header tabel dengan latar sedikit lebih gelap, hover state pada baris.
  - Form input menggunakan label jelas, border tipis, radius sudut konsisten, dan state fokus yang jelas.
- **Tipografi**: gunakan font sans-serif standar (misalnya font default Tailwind/Inter), dengan hierarki ukuran teks yang jelas (judul, subjudul, label, isi).
- **Konsistensi komponen**: tombol, badge status (menunggu/disetujui/ditolak, aktif/nonaktif), dan alert notifikasi harus punya gaya seragam di seluruh halaman.
- **Responsif**: tampilan tetap rapi di perangkat mobile, mengingat fitur presensi kemungkinan besar diakses lewat HP untuk mengambil foto dan lokasi GPS.

---

## 6. Fitur Teknis Presensi Berbasis Lokasi

- Saat staff melakukan presensi masuk/keluar, ambil koordinat GPS perangkat (browser Geolocation API) dan bandingkan dengan `latitude`, `longitude`, serta `radius` pada tabel `lokasi_presensi` sesuai `lokasi_presensi` milik pegawai tersebut.
- Jika berada di luar radius yang ditentukan, tampilkan pesan penolakan presensi dengan jelas (tanpa emoji, gunakan icon peringatan).
- Wajibkan pengambilan foto (melalui kamera perangkat) sebagai bukti kehadiran, disimpan ke kolom `foto_masuk`/`foto_keluar`.
- Bandingkan jam presensi dengan `jam_masuk`/`jam_pulang` pada `lokasi_presensi` untuk menentukan status (tepat waktu/terlambat), sesuaikan dengan `zona_waktu`.

---

## 7. Struktur Halaman

### Admin
1. Dashboard (ringkasan kehadiran hari ini, jumlah izin menunggu persetujuan)
2. Data Pegawai (CRUD)
3. Data Jabatan (CRUD)
4. Data Lokasi Presensi (CRUD)
5. Rekap Presensi (filter per tanggal, pegawai, lokasi)
6. Pengajuan Izin/Ketidakhadiran (approve/reject)
7. Manajemen Akun/User
8. Pengaturan Profil Admin

### Staff
1. Dashboard (status presensi hari ini)
2. Presensi Masuk/Keluar (dengan validasi GPS & foto)
3. Riwayat Presensi Pribadi
4. Pengajuan Izin/Ketidakhadiran
5. Status Pengajuan Izin
6. Pengaturan Profil Pribadi

---

## 8. Teknologi yang Digunakan

- **Framework**: **Laravel** (gunakan versi Laravel terbaru yang stabil).
- **Frontend**: **Blade Template** dengan **Tailwind CSS** sebagai styling utama (install via Laravel Vite: `npm install -D tailwindcss` sesuai panduan resmi Laravel + Vite). Boleh gunakan Alpine.js untuk interaktivitas ringan (modal, dropdown, toggle) tanpa perlu framework JS berat seperti React/Vue.
- **Database**: MySQL/MariaDB dengan nama database `presensi`, dikelola melalui **Migration** dan **Seeder** Laravel (bukan file `.sql` manual), lihat detail struktur pada bagian 9.
- **Autentikasi**: Gunakan Laravel built-in authentication (**Laravel Breeze** dengan stack Blade, atau buat custom auth) dengan middleware untuk membedakan akses berdasarkan `role` (`admin`/`staff`) dan pengecekan `status` (`aktif`/`nonaktif`) pada model `User`.
- **Geolocation**: Browser Geolocation API (JavaScript) untuk mengambil koordinat perangkat, dikirim ke backend Laravel via AJAX/Fetch untuk divalidasi terhadap data `lokasi_presensi`.
- **Upload Foto**: gunakan Laravel Storage (`php artisan storage:link`) untuk menyimpan foto presensi dan lampiran file ketidakhadiran ke folder `storage/app/public`.
- **Struktur Folder**: ikuti konvensi Laravel standar — Controller terpisah per modul (`AuthController`, `PegawaiController`, `PresensiController`, `KetidakhadiranController`, `JabatanController`, `LokasiPresensiController`, `UserController`), Model sesuai tabel, Request class untuk validasi input, dan Policy/Middleware untuk otorisasi role.

---

## 9. Struktur Migration & Model Laravel

Ikuti struktur berikut agar sesuai dengan skema database pada bagian 3, namun diimplementasikan dengan cara Laravel:

- **Migration**: buat file migration untuk setiap tabel (`jabatans`, `lokasi_presensis`, `pegawais`, `users` (modifikasi tabel bawaan Laravel untuk menambah `id_pegawai`, `status`, `role`), `presensis`, `ketidakhadirans`), lengkap dengan `foreignId()->constrained()` untuk relasi ke `pegawai`.
- **Model & Relasi**:
  - `Pegawai` → `hasOne(User::class)`, `hasMany(Presensi::class)`, `hasMany(Ketidakhadiran::class)`.
  - `User` → `belongsTo(Pegawai::class)`.
  - `Presensi` → `belongsTo(Pegawai::class)`.
  - `Ketidakhadiran` → `belongsTo(Pegawai::class)`.
  - `Jabatan` dan `LokasiPresensi` sebagai referensi data master (bisa direlasikan ke `Pegawai` via nama, atau idealnya diubah menjadi `foreignId` bila ingin relasi lebih ketat — opsional, sesuai preferensi implementasi).
- **Seeder**: gunakan `DatabaseSeeder` untuk memanggil `JabatanSeeder`, `LokasiPresensiSeeder`, `PegawaiSeeder`, `UserSeeder`, `PresensiSeeder`, `KetidakhadiranSeeder` — isi datanya mengikuti dummy data pada bagian 4, dengan password di-hash menggunakan `Hash::make()`.
- **Factory**: opsional, buat Factory untuk `Pegawai` dan `Presensi` agar mudah generate data uji tambahan.