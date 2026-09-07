<?php

namespace Database\Seeders;

use App\Models\Jabatan;
use App\Models\Pelatihan;
use App\Models\Modul;
use App\Models\Materi;
use App\Models\PilihanJawaban;
use App\Models\Soal;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $jabatan = Jabatan::first();
        if (!$jabatan) return;

        // Demo Admin
        $admin = User::firstOrCreate(
            ['nip' => '197501012000011001'],
            [
                'nama' => 'Admin Demo',
                'email' => 'admin@lms-passulsel.id',
                'golongan' => 'III/c',
                'jabatan_id' => $jabatan->id,
                'role' => 'admin',
                'status_akun' => 'approved',
                'password' => Hash::make('197501012000011001'),
                'force_change_password' => true,
            ]
        );

        // Demo Peserta
        $peserta = User::firstOrCreate(
            ['nip' => '198001012005011002'],
            [
                'nama' => 'Budi Santoso',
                'email' => 'budi@lms-passulsel.id',
                'golongan' => 'III/b',
                'jabatan_id' => $jabatan->id,
                'role' => 'peserta',
                'status_akun' => 'approved',
                'password' => Hash::make('198001012005011002'),
                'force_change_password' => false,
            ]
        );

        // Demo Pelatihan
        $pelatihan = Pelatihan::firstOrCreate([
            'judul' => 'Orientasi Dasar Pemasyarakatan',
        ], [
            'deskripsi' => 'Pelatihan wajib untuk seluruh pegawai di lingkungan Kantor Wilayah Ditjen Pemasyarakatan Sulawesi Selatan.',
            'is_active' => true,
        ]);

        // Demo Materi
        $materis = [
            [
                'judul' => 'Pengantar Tugas Pokok',
                'deskripsi' => 'Pengenalan menyeluruh tentang tupoksi dan kewenangan.',
                'jenis' => 'link',
                'url_link' => 'https://ditjenpas.go.id/profil',
                'pelatihan_id' => $pelatihan->id,
                'urutan' => 1,
                'durasi_baca' => 15,
            ],
            [
                'judul' => 'Peraturan dan Regulasi Pemasyarakatan',
                'deskripsi' => 'Mempelajari dasar hukum jabatan struktural di pemasyarakatan.',
                'jenis' => 'link',
                'url_link' => 'https://ditjenpas.go.id/regulasi',
                'pelatihan_id' => $pelatihan->id,
                'urutan' => 2,
                'durasi_baca' => 20,
            ],
            [
                'judul' => 'Overview Arsitektur (Video)',
                'deskripsi' => 'Video orientasi resmi Direktorat Jenderal Pemasyarakatan.',
                'jenis' => 'video_embed',
                'url_link' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'pelatihan_id' => $pelatihan->id,
                'urutan' => 3,
                'durasi_baca' => 30,
            ],
        ];

        foreach ($materis as $m) {
            Materi::firstOrCreate(['judul' => $m['judul'], 'pelatihan_id' => $m['pelatihan_id']], $m);
        }

        // Demo Kuis (Materi tipe Kuis)
        $materiKuis = Materi::firstOrCreate([
            'judul' => 'Kuis Evaluasi Pemahaman',
            'pelatihan_id' => $pelatihan->id,
        ], [
            'deskripsi' => 'Uji pemahaman tentang orientasi Bab 1.',
            'jenis' => 'quiz',
            'urutan' => 4,
            'durasi_baca' => 30,
            'passing_grade' => 70,
            'durasi_menit' => 15,
            'max_attempts' => 3,
            'acak_soal' => true,
            'acak_jawaban' => true,
        ]);

        // Demo Soal
        $soals = [
            [
                'pertanyaan' => 'Apakah tugas pokok utama Pemasyarakatan?',
                'tipe' => 'pilihan_ganda',
                'bobot' => 10,
                'pembahasan' => 'Tugas pokok utama adalah melakukan bimbingan narapidana.',
                'pilihan' => [
                    ['huruf' => 'A', 'teks' => 'Mengurus administrasi', 'is_correct' => false],
                    ['huruf' => 'B', 'teks' => 'Melakukan bimbingan narapidana', 'is_correct' => true],
                    ['huruf' => 'C', 'teks' => 'Mengelola keamanan', 'is_correct' => false],
                    ['huruf' => 'D', 'teks' => 'Menyusun laporan', 'is_correct' => false],
                ],
            ],
            [
                'pertanyaan' => 'Program Asimilasi bagi narapidana merupakan bagian dari proses pembinaan tahap apa?',
                'tipe' => 'pilihan_ganda',
                'bobot' => 10,
                'pembahasan' => 'Asimilasi adalah bagian dari pembinaan tahap akhir (integrasi).',
                'pilihan' => [
                    ['huruf' => 'A', 'teks' => 'Pembinaan Tahap Awal', 'is_correct' => false],
                    ['huruf' => 'B', 'teks' => 'Pembinaan Tahap Lanjutan I', 'is_correct' => false],
                    ['huruf' => 'C', 'teks' => 'Pembinaan Tahap Lanjutan II', 'is_correct' => false],
                    ['huruf' => 'D', 'teks' => 'Pembinaan Tahap Akhir (Integrasi)', 'is_correct' => true],
                ],
            ],
        ];

        foreach ($soals as $s) {
            $pilihan = $s['pilihan'];
            unset($s['pilihan']);
            $soal = Soal::firstOrCreate(['pertanyaan' => $s['pertanyaan']], $s);
            
            // Attach to Quiz
            if (!$materiKuis->soals()->where('soal_id', $soal->id)->exists()) {
                $materiKuis->soals()->attach($soal->id);
            }

            if ($soal->pilihanJawaban()->count() === 0) {
                foreach ($pilihan as $p) {
                    $soal->pilihanJawaban()->create($p);
                }
            }
        }
    }
}
