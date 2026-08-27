# Spesifikasi Implementasi: Quiz Interaktif (Mode Game)

## Ringkasan Eksekutif

Dokumen ini menjelaskan fitur quiz interaktif yang ditambahkan ke sistem quiz yang sudah ada di project ini dengan pendekatan extend-on-existing-architecture, bukan membuat sistem quiz baru dari nol.

Tujuan utamanya:
- mempertahankan compatibility dengan flow LMS yang sudah berjalan,
- menambah pengalaman belajar yang lebih seru dan memotivasi,
- tetap menjaga integrasi dengan penilaian, laporan, dan data quiz yang sudah ada.

### Daftar Isi
1. Ringkasan Eksekutif
2. Struktur Sistem Saat Ini
3. Prinsip Arsitektur Implementasi
4. Data / Field yang Harus Ditambah
5. Flow Backend dan Sesi Quiz
6. Struktur Data dan Relasi
7. Layout Konfigurasi di Admin
8. Prioritas Implementasi dan MVP
9. Acceptance Criteria

---

## 1. Struktur Sistem Saat Ini Yang Dipakai

Project sudah memiliki komponen utama berikut:

- [app/Models/Materi.php](app/Models/Materi.php)
  - field seperti passing_grade, durasi_menit, max_attempts, acak_soal,
    acak_jawaban, tampilkan_feedback, strict_anti_cheat
- [app/Models/Soal.php](app/Models/Soal.php)
  - model soal, tipe soal, bobot, pembahasan, pilihan jawaban
- [app/Models/SesiEvaluasi.php](app/Models/SesiEvaluasi.php)
  - sesi pengerjaan quiz per user per materi
- [app/Models/HasilLatihan.php](app/Models/HasilLatihan.php)
  - menyimpan jawaban user per soal
- [app/Http/Controllers/EvaluasiController.php](app/Http/Controllers/EvaluasiController.php)
  - flow start -> soal -> submit -> hasil
- [resources/views/admin/materi/edit.blade.php](resources/views/admin/materi/edit.blade.php)
  - form konfigurasi kuis yang sudah memiliki bagian penilaian dan perilaku soal

Berdasarkan struktur ini, fitur game tidak perlu membuat tabel baru secara besar. Yang perlu ditambah adalah field konfigurasi kuis dan metadata sesi game.

---

## 2. Prinsip Arsitektur Implementasi

### 2.1 Extend, bukan replace
- Form quiz lama tetap dipakai.
- Mode standard tetap berjalan tanpa error.
- Mode interaktif hanya aktif jika pengaturan baru di-enable.

### 2.2 Reuse model dan data yang sudah ada
- Soal tetap di model Soal.
- Jawaban peserta tetap di HasilLatihan.
- Sesi pengerjaan tetap di SesiEvaluasi.
- Materi tetap menjadi entity utama yang menampung setting game.

### 2.3 Tambahan field bertujuan bukan duplikasi data
Semua fitur game akan disimpan sebagai pengaturan materi dan sesi evaluasi, bukan tabel baru yang terpisah.

---

## 3. Data / Field yang Harus Ditambah

### 4.1 Field tambahan di tabel materi
Kolom ini idealnya ditambahkan ke tabel materis agar konfigurasi quiz bisa disimpan di satu tempat.

| Nama field | Tipe | Default | Keterangan |
|---|---|---:|---|
| mode_tampilan | string | standard | standard / interaktif |
| sub_mode | string | standard | standard / time_attack / practice |
| timer_per_soal | integer | 0 | 0 berarti tidak pakai timer per soal |
| sound_enabled | boolean | true | efek suara aktif atau tidak |
| leaderboard_enabled | boolean | false | leaderboard diaktifkan atau tidak |
| bonus_kecepatan_enabled | boolean | false | bonus skor berdasarkan cepat menjawab |
| animasi_enabled | boolean | true | efek feedback/transition aktif |
| badge_enabled | boolean | false | badge per sesi atau modul aktif |
| show_answer_review | boolean | true | menampilkan pembahasan/poin jawaban benar setelah selesai |
| theme_name | string | default | opsi tema visual quiz |

### 4.2 Field tambahan di tabel sesi_evaluasis
Karena sesi adalah eksekusi pengerjaan, field ini dibutuhkan untuk menyimpan state game.

| Nama field | Tipe | Default | Keterangan |
|---|---|---:|---|
| current_soal_index | integer | 0 | soal yang sedang aktif |
| jawaban_tersimpan | json | null | cache jawaban user per soal |
| waktu_mulai_soal | datetime | null | timestamp soal saat mulai |
| waktu_terakhir_aksi | datetime | null | untuk auto-save / resume |
| streak | integer | 0 | jumlah jawaban benar beruntun |
| xp_earned | integer | 0 | xp yang diperoleh sesi |
| is_paused | boolean | false | status pause jika diperlukan |
| last_activity_at | datetime | null | log aktivitas terakhir |
| tab_blur_count | integer | 0 | anti-cheat dasar |

### 4.3 Field tambahan di tabel hasil_latihans
Untuk log detail jawaban, sangat cocok ditambahkan:

| Nama field | Tipe | Default | Keterangan |
|---|---|---:|---|
| is_skipped | boolean | false | apakah soal dilewati |
| response_time_seconds | integer | 0 | lama waktu menjawab soal |
| answer_order | json | null | urutan opsi yang muncul saat menjawab |
| feedback_shown | boolean | false | feedback sudah ditampilkan atau belum |

---

## 5. Flow Implementasi yang Harus Dipastikan

### 5.1 Flow start quiz
Aktual di project sekarang:
- user menekan tombol mulai kuis dari materi
- route: `peserta.evaluasi.start`
- controller: `EvaluasiController::start()`
- dibuat sesi baru di SesiEvaluasi
- redirect ke halaman soal

Untuk mode interaktif, flow tersebut tetap dipertahankan. Yang ditambah:
- membaca `mode_tampilan` dari materi
- menyiapkan `timer_per_soal` jika mode aktif
- menyimpan `jawaban_tersimpan` awal sebagai array kosong
- menghitung `xp` awal dan status sesi

### 5.2 Flow saat mengerjakan soal
Halaman soal yang sekarang ada di `resources/views/peserta/evaluasi/soal.blade.php` harus dibagi menjadi dua mode:
- standard: render form biasa
- interaktif: render panel game, timer per soal, progress, feedback instant

Logika di backend:
- jawaban diproses via submit AJAX atau form biasa
- tiap perubahan jawaban disimpan otomatis ke `jawaban_tersimpan`
- per soal, hitung `response_time_seconds`
- jika waktu habis, otomatis masukkan jawaban kosong / skip

### 5.3 Flow saat submit jawaban
Controller submit saat ini di `EvaluasiController::submit()` dan `processSubmit()`. Untuk mode interaktif, rules berikut harus diterapkan:
- validasi skor berdasarkan database, bukan hanya client timer
- kalkulasi streak berdasarkan jawaban benar berturut-turut
- hitung bonus kecepatan jika `bonus_kecepatan_enabled` aktif
- simpan log `response_time_seconds`, `is_skipped`, `feedback_shown`

### 5.4 Flow hasil akhir
Setelah submit, system sudah redirect ke route hasil. Untuk mode game, halaman hasil harus menampilkan:
- skor akhir
- jumlah benar dan salah
- streak tertinggi
- xp earned
- badge jika memenuhi syarat
- tombol retry untuk practice atau tombol lihat leaderboard

---

## 6. Struktur Data dan Relasi

### 6.1 Struktur utama tetap sama
Tidak perlu menambah model baru untuk fitur dasar. Yang relevan:

- Materi: pengaturan mode quiz
- Soal: inti konten kuis
- PilihanJawaban: opsi jawaban
- SesiEvaluasi: satu attempt dari siswa
- HasilLatihan: jawaban per soal di attempt

### 6.2 Relasi yang perlu dijaga
- Materi hasMany SesiEvaluasi
- SesiEvaluasi belongsTo Materi
- SesiEvaluasi hasMany HasilLatihan
- Soal hasMany HasilLatihan
- HasilLatihan belongsTo SesiEvaluasi, Soal, User

Ini penting agar report analytics tetap konsisten dengan sistem yang sekarang sudah ada.

---

## 7. Revisi Fitur Berdasarkan Arsitektur Existing

### 7.1 Timer per soal
Timer tidak harus dibuat sebagai tabel baru. Cukup dibuat sebagai field di materi + metadata sesi.

### 7.2 Sound dan animasi
Tidak perlu data model khusus. Sound/animasi di-handle di frontend Blade + JS.

### 7.3 Streak counter
Streak bisa dihitung saat submit, tidak perlu stored per soal jika tidak ingin mempersulit struktur.

### 7.4 Leaderboard
Leaderboard sebaiknya dibuat sebagai view/report per materi, bukan tabel independen di MVP. Untuk MVP, cukup menggunakan data dari `sesi_evaluasis` dan ranking by skor, lalu waktu tercepat.

### 7.5 Badge / achievement
Badge dapat dibuat dari rule-based engine sederhana, tidak harus tabel kompleks di awal. Contoh:
- `perfect_score`: skor === 100%
- `fast_finisher`: waktu <= threshold
- `streak_3`: streak >= 3

---

## 8. Implementasi Laravel yang Relevan

### 8.1 Migration yang disarankan
Untuk update feature, migration paling aman adalah menambahkan field ke tabel yang ada.

Contoh:

```php
Schema::table('materis', function (Blueprint $table) {
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
});

Schema::table('sesi_evaluasis', function (Blueprint $table) {
    $table->unsignedInteger('current_soal_index')->default(0);
    $table->json('jawaban_tersimpan')->nullable();
    $table->dateTime('waktu_mulai_soal')->nullable();
    $table->dateTime('waktu_terakhir_aksi')->nullable();
    $table->unsignedInteger('streak')->default(0);
    $table->unsignedInteger('xp_earned')->default(0);
    $table->boolean('is_paused')->default(false);
    $table->dateTime('last_activity_at')->nullable();
    $table->unsignedInteger('tab_blur_count')->default(0);
});

Schema::table('hasil_latihans', function (Blueprint $table) {
    $table->boolean('is_skipped')->default(false);
    $table->unsignedInteger('response_time_seconds')->default(0);
    $table->json('answer_order')->nullable();
    $table->boolean('feedback_shown')->default(false);
});
```

### 8.2 Model cast
Pada model yang sudah ada, tambahkan casts untuk field boolean/json:

```php
protected $casts = [
    'sound_enabled' => 'boolean',
    'leaderboard_enabled' => 'boolean',
    'animasi_enabled' => 'boolean',
    'show_answer_review' => 'boolean',
    'jawaban_tersimpan' => 'array',
    'is_paused' => 'boolean',
    'is_skipped' => 'boolean',
];
```

### 8.3 Controller adaptation
File yang utama berubah:
- [app/Http/Controllers/EvaluasiController.php](app/Http/Controllers/EvaluasiController.php)
- [app/Http/Controllers/MateriController.php](app/Http/Controllers/MateriController.php)
- [resources/views/peserta/evaluasi/soal.blade.php](resources/views/peserta/evaluasi/soal.blade.php)
- [resources/views/peserta/evaluasi/hasil.blade.php](resources/views/peserta/evaluasi/hasil.blade.php)

### 8.4 View adaptation
Form admin di [resources/views/admin/materi/edit.blade.php](resources/views/admin/materi/edit.blade.php) sudah cocok sebagai tempat menaruh section baru `Mode Interaktif`.

---

## 9. Daftar Fitur MVP yang Realistis

### Wajib ada
1. `mode_tampilan` standard/interaktif di materi
2. `timer_per_soal` dan timer visible di UI
3. feedback instant benar/salah
4. auto-save jawaban per soal
5. hasil akhir dengan skor, benar/salah, persentase
6. keep compatibility dengan mode standard

### Prioritas berikutnya
1. streak counter
2. icon badge/achievement
3. leaderboard sederhana per materi
4. bonus kecepatan
5. sound toggle

### Nice-to-have
1. tema visual custom per quiz
2. anti-cheat tab blur logging
3. real-time leaderboard via broadcast

---

## 10. Acceptance Criteria

### 10.1 Untuk siswa
- User bisa mulai quiz dengan mode standard atau interaktif.
- Pada mode interaktif, timer dan feedback visual muncul.
- Sistem tetap mengirim jawaban ke backend yang sama.
- Jika refresh browser, jawaban yang terakhir dipilih masih tersimpan.
- Halaman hasil menampilkan skor dan statistik.

### 10.2 Untuk guru
- Guru bisa mengubah mode tampilan dari form materi yang sudah ada.
- Setting game bisa disimpan ke materi.
- Guru bisa melihat apakah quiz aktif di mode standard atau interaktif.
- Data hasil siswa tetap aktual dan dapat dilihat di laporan quiz yang sudah ada.

### 10.3 Untuk developer
- Tidak ada perubahan besar pada model utama.
- Tidak ada duplikasi data soal.
- Semua fitur game terikat pada materi, sesi, dan hasil latihan yang sudah ada.

---

## 11. Kesimpulan

Implementasi quiz game yang paling cocok untuk project ini adalah pendekatan progressive enhancement: tetap memakai model dan flow yang sudah ada, lalu menambahkan konfigurasi mode interaktif, metadata sesi, dan UI game di frontend.

Dengan pendekatan ini, perubahan lebih aman, lebih cepat diproduksi, dan tidak merusak sistem penilaian yang sudah berjalan.

---

## 12. Rekomendasi Urutan Pengerjaan

1. Tambahkan field konfigurasi mode interaktif di tabel materi.
2. Tambahkan metadata sesi game di tabel sesi_evaluasis.
3. Update model `Materi` dan `SesiEvaluasi` dengan casts yang relevan.
4. Modifikasi `EvaluasiController` agar membaca mode dari materi.
5. Update `resources/views/peserta/evaluasi/soal.blade.php` untuk mode interaktif.
6. Update hasil page dan laporan.
7. Tambahkan toggle di form admin materi.

Ini adalah urutan paling realistis untuk project Laravel yang sudah ada.
