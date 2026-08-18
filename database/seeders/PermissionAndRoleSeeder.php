<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class PermissionAndRoleSeeder extends Seeder
{
    public function run(): void
    {
        // ============================
        // 1. Seed Permissions
        // ============================
        $permissions = [
            // Grup: Konten
            ['kode' => 'kelola_pelatihan',  'nama' => 'Kelola Pelatihan & Materi', 'deskripsi' => 'Membuat, mengedit, dan menghapus pelatihan beserta materinya.', 'grup' => 'Konten', 'urutan' => 1],
            ['kode' => 'kelola_soal',       'nama' => 'Kelola Soal & Kuis',        'deskripsi' => 'Membuat, mengedit, dan menghapus soal kuis pada materi.', 'grup' => 'Konten', 'urutan' => 2],
            ['kode' => 'import_soal',       'nama' => 'Import Soal dari CSV',      'deskripsi' => 'Mengimpor soal secara massal menggunakan file CSV.', 'grup' => 'Konten', 'urutan' => 3],

            // Grup: Evaluasi & Kuis
            ['kode' => 'review_kuis',       'nama' => 'Review & Nilai Kuis Peserta', 'deskripsi' => 'Melihat jawaban dan memberikan nilai manual pada kuis peserta.', 'grup' => 'Evaluasi', 'urutan' => 4],
            ['kode' => 'export_nilai',      'nama' => 'Export Nilai Kuis',           'deskripsi' => 'Mengekspor rekap nilai kuis peserta ke file.', 'grup' => 'Evaluasi', 'urutan' => 5],
            ['kode' => 'ikut_evaluasi',     'nama' => 'Ikut Evaluasi / Kuis',        'deskripsi' => 'Mengikuti sesi evaluasi/kuis sebagai peserta.', 'grup' => 'Evaluasi', 'urutan' => 6],

            // Grup: Pengguna
            ['kode' => 'kelola_akun',       'nama' => 'Manajemen Akun',           'deskripsi' => 'Menyetujui, menolak, mereset password, dan menghapus akun pengguna.', 'grup' => 'Pengguna', 'urutan' => 7],
            ['kode' => 'kelola_akses',      'nama' => 'Kelola Akses Fitur',       'deskripsi' => 'Membuat role kustom, mengatur permission, dan mengassign role ke user.', 'grup' => 'Pengguna', 'urutan' => 8],

            // Grup: Laporan
            ['kode' => 'lihat_statistik',   'nama' => 'Statistik Peserta (Admin)',   'deskripsi' => 'Melihat rekap nilai, progres, dan statistik seluruh peserta.', 'grup' => 'Laporan', 'urutan' => 9],
            ['kode' => 'lihat_statistik_sendiri', 'nama' => 'Statistik Pribadi (Peserta)', 'deskripsi' => 'Melihat statistik dan riwayat belajar sendiri.', 'grup' => 'Laporan', 'urutan' => 10],

            // Grup: Sistem
            ['kode' => 'kelola_kalender',   'nama' => 'Kalender Akademik',   'deskripsi' => 'Menambah, mengedit, dan menghapus event kalender akademik.', 'grup' => 'Sistem', 'urutan' => 11],
            ['kode' => 'ai_assistant',      'nama' => 'AI Assistant',        'deskripsi' => 'Menggunakan fitur AI Assistant untuk tanya jawab materi.', 'grup' => 'Sistem', 'urutan' => 12],

            // Grup: Pembelajaran (Peserta)
            ['kode' => 'lihat_pelatihan',   'nama' => 'Lihat Katalog Pelatihan', 'deskripsi' => 'Mengakses dan mempelajari konten pelatihan yang tersedia.', 'grup' => 'Pembelajaran', 'urutan' => 13],
        ];

        foreach ($permissions as $p) {
            Permission::updateOrCreate(['kode' => $p['kode']], $p);
        }

        // ============================
        // 2. Seed Default Roles
        // ============================
        // Superadmin: semua permission
        $superadminRole = Role::updateOrCreate(
            ['nama' => 'Superadmin'],
            ['deskripsi' => 'Akses penuh ke seluruh sistem. Role ini dilindungi dan tidak bisa diedit.', 'is_default' => true, 'base_role' => 'superadmin']
        );
        $superadminRole->permissions()->sync(Permission::pluck('id'));

        // Admin: semua kecuali kelola_akses dan fitur peserta
        $adminRole = Role::updateOrCreate(
            ['nama' => 'Admin'],
            ['deskripsi' => 'Akses pengelolaan konten, akun, dan laporan. Tidak dapat mengelola akses fitur.', 'is_default' => true, 'base_role' => 'admin']
        );
        $adminPerms = Permission::whereIn('kode', [
            'kelola_pelatihan', 'kelola_soal', 'import_soal', 'review_kuis',
            'export_nilai', 'kelola_akun', 'lihat_statistik', 'kelola_kalender',
        ])->pluck('id');
        $adminRole->permissions()->sync($adminPerms);

        // Peserta: hanya fitur belajar
        $pesertaRole = Role::updateOrCreate(
            ['nama' => 'Peserta'],
            ['deskripsi' => 'Akses untuk mengikuti pelatihan, kuis, dan melihat statistik pribadi.', 'is_default' => true, 'base_role' => 'peserta']
        );
        $pesertaPerms = Permission::whereIn('kode', [
            'lihat_pelatihan', 'ikut_evaluasi', 'lihat_statistik_sendiri', 'ai_assistant',
        ])->pluck('id');
        $pesertaRole->permissions()->sync($pesertaPerms);

        $this->command->info('✅ Permissions dan Default Roles berhasil di-seed!');
    }
}
