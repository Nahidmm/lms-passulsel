# Tata Letak Konfigurasi Quiz Interaktif (Implementasi Laravel)

## Ringkasan

Dokumen ini mengatur tata letak konfigurasi quiz agar tetap konsisten dengan form yang sudah ada di project, terutama di [resources/views/admin/materi/edit.blade.php](resources/views/admin/materi/edit.blade.php).

Tujuan utamanya:
- tidak mengubah pola yang sudah familiar bagi admin dan guru,
- menambahkan fitur game sebagai section tambahan yang tidak memusingkan pengguna biasa,
- tetap mudah diimplementasikan dengan Laravel dan Blade.

### Daftar Isi
1. Ringkasan
2. Struktur Form Admin yang Sudah Sesuai
3. Layout Section Mode Interaktif
4. Mapping Field ke Struktur Laravel
5. Data yang Harus Ditambahkan
6. Dashboard Guru dan Laporan
7. Responsif untuk Mobile
8. Prioritas Pengerjaan

---

## 1. Struktur Form Admin yang Sudah Sesuai

Form admin kuis saat ini sudah terbagi ke dalam beberapa area logis:
- Informasi Dasar
- Penilaian & Waktu
- Perilaku Soal

Struktur ini sudah sangat cocok untuk ditambahkan bagian baru:

```
[Back to Pelatihan]                 [Import Soal] [Nilai Peserta]

┌──────────────────────────────────────────────────────────────┐
│  Informasi Dasar                                             │
│  Judul Kuis                                                 │
│  Instruksi Peserta                                           │
├──────────────────────────────────────────────────────────────┤
│  Penilaian & Waktu                                           │
│  Passing Grade | Durasi | Max Attempt | Poin                 │
├──────────────────────────────────────────────────────────────┤
│  Perilaku Soal                                               │
│  Acak Soal | Acak Jawaban | Tampilkan Feedback              │
├──────────────────────────────────────────────────────────────┤
│  Mode Interaktif (BARU)                                      │
│  Mode Tampilan | Sub-mode | Timer | Sound | Leaderboard      │
├──────────────────────────────────────────────────────────────┤
│  Simpan / Publish                                             │
└──────────────────────────────────────────────────────────────┘
```

Ini lebih realistis daripada membuat tab baru yang benar-benar terpisah dari form saat ini.

---

## 3. Layout Rekomendasi untuk Section Mode Interaktif

### 3.1 Posisi section
Taruh section `Mode Interaktif` tepat setelah `Perilaku Soal`, karena ini masih berkaitan dengan mekanisme quiz saat pengerjaan.

### 3.2 Bentuk field
Gunakan pola berikut:
- radio card untuk `Mode Tampilan`:
  - Standard
  - Interaktif (Game)
- select dropdown untuk `Sub-mode`:
  - Standard
  - Time Attack
  - Practice
- toggle switch untuk pengaturan tambahan

### 3.3 Contoh struktur UI

```
┌────────────────────────────────────────────┬──────────────────────────────┐
│ Mode Interaktif                          │ Quick Preview               │
├────────────────────────────────────────────┼──────────────────────────────┤
│ Mode Tampilan:                            │ [Soal 1/10]                 │
│ [ ] Standard                             │  Timer: 12s                 │
│ [•] Interaktif (Game)                    │  Skor: 250                  │
│                                          │  Streak: 3x                 │
│                                          │  [Jawab] button            │
│ Sub-mode: [Time Attack ▼]               │                            │
│                                          │                            │
│ Efek suara           [ON/OFF]            │                            │
│ Waktu per soal       [20 detik]         │                            │
│ Leaderboard          [ON/OFF]            │                            │
│ Animasi feedback     [ON/OFF]            │                            │
│ Badge / achievement  [ON/OFF]            │                            │
│ Bonus skor kecepatan [ON/OFF]            │                            │
└────────────────────────────────────────────┴──────────────────────────────┘
```

### 3.4 Kriteria UX
- Tidak tampilkan seluruh konfigurasi game jika mode = Standard.
- Ketika mode = Interaktif, munculkan block baru yang terpisah.
- Gunakan `toggle switch` untuk mengaktifkan/nonaktifkan fitur.
- Preview bisa minimal saja, cukup menunjukkan status soal, timer, skor, stamina/streak.

---

## 4. Mapping Field ke Current Laravel Structure

Berikut mapping antara layout admin dan model yang sudah ada:

| Section di form | Field di model | Status |
|---|---|---|
| Judul Kuis | `materis.judul` | Sudah ada |
| Instruksi | `materis.deskripsi` | Sudah ada |
| Passing Grade | `materis.passing_grade` | Sudah ada |
| Durasi | `materis.durasi_menit` | Sudah ada |
| Max Attempt | `materis.max_attempts` | Sudah ada |
| Acak Soal | `materis.acak_soal` | Sudah ada |
| Acak Jawaban | `materis.acak_jawaban` | Sudah ada |
| Tampilkan Feedback | `materis.tampilkan_feedback` | Sudah ada |
| Mode Interaktif | `materis.mode_tampilan` | Baru |
| Sub-mode | `materis.sub_mode` | Baru |
| Timer Per Soal | `materis.timer_per_soal` | Baru |
| Sound | `materis.sound_enabled` | Baru |
| Leaderboard | `materis.leaderboard_enabled` | Baru |
| Animasi | `materis.animasi_enabled` | Baru |
| Badge | `materis.badge_enabled` | Baru |

---

## 5. Struktur Data yang Harus Ditambahkan

### 5.1 Di tabel materis
Field yang baru ditambahkan untuk mengatur mode game di materi:

```php
$table->string('mode_tampilan')->default('standard');
$table->string('sub_mode')->default('standard');
$table->unsignedInteger('timer_per_soal')->default(0);
$table->boolean('sound_enabled')->default(true);
$table->boolean('leaderboard_enabled')->default(false);
$table->boolean('bonus_kecepatan_enabled')->default(false);
$table->boolean('animasi_enabled')->default(true);
$table->boolean('badge_enabled')->default(false);
$table->boolean('show_answer_review')->default(true);
$table->string('theme_name')->nullable();
```

### 5.2 Di tabel sesi_evaluasis
Field ini diperlukan untuk status gameplay saat siswa sedang mengerjakan:

```php
$table->unsignedInteger('current_soal_index')->default(0);
$table->json('jawaban_tersimpan')->nullable();
$table->dateTime('waktu_mulai_soal')->nullable();
$table->dateTime('waktu_terakhir_aksi')->nullable();
$table->unsignedInteger('streak')->default(0);
$table->unsignedInteger('xp_earned')->default(0);
$table->boolean('is_paused')->default(false);
$table->dateTime('last_activity_at')->nullable();
$table->unsignedInteger('tab_blur_count')->default(0);
```

### 5.3 Di tabel hasil_latihans
Field ini untuk audit dan statistik jawab per soal:

```php
$table->boolean('is_skipped')->default(false);
$table->unsignedInteger('response_time_seconds')->default(0);
$table->json('answer_order')->nullable();
$table->boolean('feedback_shown')->default(false);
```

---

## 6. Form Dashboard Guru: List Quiz

Pada dashboard list quiz, guru perlu melihat langsung mode setiap kuis tanpa masuk detail.

```
┌──────────────────────────────────────────────────────────────────┐
│ Quiz Saya                                     [+ Buat Quiz Baru] │
├──────────────────────────────────────────────────────────────────┤
│ 🎮 Quiz Bab 3: Fotosintesis      [Interaktif] [Published]     ⋮ │
│ 📝 Quiz Bab 2: Sel Hewan        [Standard]   [Published]     ⋮ │
│ 🎮 Kuis Cepat: Rumus Kimia      [Interaktif] [Draft]         ⋮ │
└──────────────────────────────────────────────────────────────────┘
```

Kriteria:
- badge mode cepat terbaca
- ikon bisa berbeda untuk standard vs interaktif
- menu aksi tetap konsisten dengan menu yang sudah ada

---

## 7. Halaman Hasil Quiz untuk Guru

Halaman laporan hasil tidak harus dibuat sangat berbeda, cukup menambah informasi tambahan jika mode interaktif aktif.

```
RINGKASAN
- Rata-rata skor
- Tingkat kelulusan
- Skor tertinggi
- Waktu terlama/tercepat
- Streak tertinggi

DETAIL SISWA
- Nama
- Skor
- Waktu selesai
- Percobaan ke-
- Status lulus
- XP / badge
```

Ini sangat cocok dengan data yang sudah ada di `SesiEvaluasi` dan `HasilLatihan`.

---

## 8. Rekomendasi UX untuk Mobile

Karena project ini digunakan untuk LMS enterprise/pegawai, mobile tetap penting. Maka:
- tab form tetap horizontal di desktop
- di mobile diubah jadi dropdown/simple accordion
- preview interaktif diletakkan di bawah form, bukan samping
- switch toggle full width agar mudah di tap

---

## 9. Prioritas Pengerjaan yang Realistis

### Phase 1 — implementasi aman
1. tambah field `mode_tampilan` dan `sub_mode`
2. tambah `timer_per_soal` dan `sound_enabled`
3. update `EvaluasiController` untuk membaca konfigurasi materi
4. update Blade soal agar menampilkan mode interaktif
5. simpan jawaban dan skor sesuai flow lama

### Phase 2 — engagement
1. streak counter
2. leaderboard sederhana per materi
3. bonus skor kecepatan
4. badge/achievement

### Phase 3 — advanced
1. real-time leaderboard
2. anti-cheat logging lebih detil
3. tema visual custom per quiz

---

## 10. Kesimpulan

Konfigurasi quiz untuk mode interaktif harus mengikuti pola form yang sudah ada di project, bukan membangun layout baru yang terpisah. Dengan pendekatan ini, form admin tetap familiar, data tetap konsisten, dan implementasi Laravel menjadi lebih aman.

Poin penting:
- data tetap di model lama (`Materi`, `SesiEvaluasi`, `HasilLatihan`)
- section game ditambahkan sebagai pengaturan tambahan
- hasil quiz tetap bisa dilihat di laporan yang sudah ada

Jadi, desain UI bisa seru, tapi arsitektur tetap clean dan sesuai dengan kode yang ada sekarang.
