# LMS Pemasyarakatan Sulawesi Selatan (SPEKTRA)

LMS SPEKTRA adalah sistem manajemen pembelajaran khusus untuk pegawai di lingkungan Kanwil Kemenkumham Sulawesi Selatan. Sistem ini dilengkapi dengan Kuis Interaktif (Cinematic Game Mode) dan Asisten AI berbasis Google Gemini.

## 🚀 Panduan Instalasi (Untuk Tim Pengembang / Kolaborasi)

Ikuti langkah-langkah di bawah ini untuk menjalankan proyek ini di perangkat lokal Anda.

### 1. Persyaratan Sistem
Pastikan perangkat Anda sudah terinstal:
- **PHP** (minimal versi 8.2)
- **Composer** (versi terbaru)
- **Node.js** dan **NPM** (minimal versi 18.x)
- **Git**

### 2. Kloning Repositori
Lakukan kloning dari repositori utama:
```bash
git clone https://github.com/Nahidmm/lms-passulsel.git
cd lms-passulsel
```

### 3. Instalasi Dependensi
Instal pustaka PHP menggunakan Composer dan dependensi Node.js menggunakan NPM:
```bash
# Instal dependensi backend (PHP)
composer install

# Instal dependensi frontend (JavaScript/CSS)
npm install
```

### 4. Konfigurasi Environment (`.env`)
Salin berkas konfigurasi *environment* dan sesuaikan:
```bash
cp .env.example .env
```
Setelah itu, buat _application key_ rahasia:
```bash
php artisan key:generate
```

Buka *file* `.env` dan pastikan konfigurasi *database* menggunakan `sqlite` (bawaan Laravel 11) agar lebih praktis untuk pengembangan lokal:
```env
DB_CONNECTION=sqlite
# Hapus baris DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD jika ada.
```
*Catatan: Pastikan Anda telah membuat *file* kosong bernama `database.sqlite` di dalam *folder* `database/` jika terjadi error saat migrasi.*

Selain itu, isi konfigurasi API Key untuk Gemini agar AI Assistant dapat berfungsi:
```env
GEMINI_API_KEY=masukkan_api_key_google_gemini_anda_di_sini
```

### 5. Migrasi dan Seeding Database
Jalankan migrasi untuk membuat tabel *database* beserta data awal (*dummy/seeder*):
```bash
php artisan migrate:fresh --seed
```
*Catatan: `SamplePretestSeeder` dan `SertifikatPermissionSeeder` sudah dipanggil otomatis melalui `DatabaseSeeder`.*

### 6. Tautkan Storage Folder
Agar file (seperti materi PDF atau gambar) yang diunggah ke folder `storage/app/public` bisa diakses dari *browser*, jalankan perintah ini:
```bash
php artisan storage:link
```

### 7. Jalankan Server Pengembangan (Development Server)
Anda perlu menjalankan **dua server** secara bersamaan. Silakan buka **dua tab terminal** yang berbeda di dalam folder proyek.

**Terminal 1 (Backend - PHP):**
```bash
php artisan serve
```

**Terminal 2 (Frontend - Vite Asset Bundler):**
```bash
npm run dev
```

### 8. Akses Aplikasi
Buka *browser* Anda dan kunjungi URL berikut:
**[http://localhost:8000](http://localhost:8000)**

---

## 👥 Informasi Kredensial Uji Coba

Untuk keperluan *testing*, Anda dapat masuk menggunakan akun berikut:

- **Superadmin:**
  - Email: `superadmin@lms.test`
  - Password: `password`
- **Peserta Budi (Sudah Reset Pretest):**
  - NIP/Username: `198001012005011002`
  - Password: `password`

## 🧩 Fitur Utama
1. **Manajemen Pembelajaran (Materi, Video, Modul)**
2. **Mesin Pretest & Kuis Interaktif (Cinematic Game Mode)**
3. **Penerbitan E-Sertifikat Otomatis**
4. **Asisten AI (RAG - *Retrieval-Augmented Generation*) berbasis Gemini 1.5/3.6**
5. **Dashboard Analitik**
