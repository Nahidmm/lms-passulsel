<?php

namespace Database\Seeders;

use App\Models\Jabatan;
use App\Models\Modul;
use App\Models\Materi;
use App\Models\PilihanJawaban;
use App\Models\Soal;
use App\Models\User;
use App\Models\Video;
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

        // Demo Modul
        $modul = Modul::firstOrCreate([
            'judul' => 'Orientasi Dasar Pemasyarakatan',
        ], [
            'deskripsi' => 'Modul wajib untuk seluruh pegawai baru Eselon V',
            'urutan' => 1,
            'is_active' => true,
        ]);

        // Demo Materi
        $materis = [
            [
                'judul' => 'Pengantar Tugas Pokok dan Fungsi Pejabat Eselon IV',
                'deskripsi' => 'Pengenalan menyeluruh tentang tupoksi, kewenangan, dan tanggung jawab pejabat struktural eselon IV di lingkungan Direktorat Jenderal Pemasyarakatan.',
                'jenis' => 'link',
                'url_link' => 'https://www.kemenkumham.go.id/profil/tupoksi',
                'modul_id' => $modul->id,
                'urutan' => 1,
                'durasi_baca' => 15,
            ],
            [
                'judul' => 'Peraturan Menteri Hukum dan HAM tentang Jabatan Struktural',
                'deskripsi' => 'Mempelajari dasar hukum jabatan struktural di pemasyarakatan, hierarki organisasi, dan kewenangan masing-masing pejabat.',
                'jenis' => 'link',
                'url_link' => 'https://www.kemenkumham.go.id/regulasi',
                'modul_id' => $modul->id,
                'urutan' => 2,
                'durasi_baca' => 20,
            ],
            [
                'judul' => 'Manajemen Pembinaan Narapidana',
                'deskripsi' => 'Strategi dan teknik pembinaan narapidana yang efektif, meliputi pendekatan individual, kelompok, dan berbasis komunitas.',
                'jenis' => 'link',
                'url_link' => 'https://ditjenpas.go.id/pembinaan',
                'modul_id' => $modul->id,
                'urutan' => 3,
                'durasi_baca' => 25,
            ],
        ];

        foreach ($materis as $m) {
            Materi::firstOrCreate(['judul' => $m['judul'], 'modul_id' => $m['modul_id']], $m);
        }

        // Demo Video
        Video::firstOrCreate(
            ['judul' => 'Orientasi Pegawai Pemasyarakatan Eselon IV'],
            [
                'deskripsi' => 'Video orientasi resmi Direktorat Jenderal Pemasyarakatan untuk pejabat eselon IV baru.',
                'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'urutan' => 1,
                'durasi_menit' => 30,
            ]
        );

        // Demo Kuis (Materi tipe Kuis)
        $materiKuis = Materi::firstOrCreate([
            'judul' => 'Kuis Orientasi',
            'modul_id' => $modul->id,
        ], [
            'deskripsi' => 'Uji pemahaman tentang orientasi',
            'jenis' => 'quiz',
            'urutan' => 4,
            'durasi_baca' => 30,
        ]);

        // Demo Soal
        $soals = [
            [
                'pertanyaan' => 'Apakah tugas pokok utama Kepala Seksi Bimbingan Narapidana/Anak Didik di Lapas?',
                'tipe' => 'pilgan',
                'materi_id' => $materiKuis->id,
                'bobot' => 10,
                'pembahasan' => 'Tugas pokok utama adalah melakukan bimbingan narapidana/anak didik dan pengawasan pelaksanaan program pembinaan.',
                'pilihan' => [
                    ['huruf' => 'A', 'teks' => 'Mengurus administrasi keuangan dan penganggaran Lapas', 'is_correct' => false],
                    ['huruf' => 'B', 'teks' => 'Melakukan bimbingan narapidana/anak didik dan pengawasan program pembinaan', 'is_correct' => true],
                    ['huruf' => 'C', 'teks' => 'Mengelola kegiatan keamanan dan ketertiban Lapas', 'is_correct' => false],
                    ['huruf' => 'D', 'teks' => 'Menyusun laporan tahunan kepada Kepala Lapas', 'is_correct' => false],
                ],
            ],
            [
                'pertanyaan' => 'Dalam hierarki organisasi Lapas, Kepala Seksi bertanggung jawab kepada siapa?',
                'tipe' => 'pilgan',
                'materi_id' => $materiKuis->id,
                'bobot' => 10,
                'pembahasan' => 'Kepala Seksi (Eselon IV) bertanggung jawab langsung kepada Kepala Lapas (Eselon III) sesuai dengan struktur organisasi.',
                'pilihan' => [
                    ['huruf' => 'A', 'teks' => 'Direktur Jenderal Pemasyarakatan', 'is_correct' => false],
                    ['huruf' => 'B', 'teks' => 'Kepala Divisi Pemasyarakatan', 'is_correct' => false],
                    ['huruf' => 'C', 'teks' => 'Kepala Lapas / Kepala Rutan', 'is_correct' => true],
                    ['huruf' => 'D', 'teks' => 'Kepala Kantor Wilayah Kemenkumham', 'is_correct' => false],
                ],
            ],
            [
                'pertanyaan' => 'Program Asimilasi bagi narapidana merupakan bagian dari proses pembinaan tahap apa?',
                'tipe' => 'pilgan',
                'materi_id' => $materiKuis->id,
                'bobot' => 10,
                'pembahasan' => 'Asimilasi adalah bagian dari pembinaan tahap akhir (integrasi), di mana narapidana dipersiapkan untuk kembali ke masyarakat.',
                'pilihan' => [
                    ['huruf' => 'A', 'teks' => 'Pembinaan Tahap Awal (Orientasi)', 'is_correct' => false],
                    ['huruf' => 'B', 'teks' => 'Pembinaan Tahap Lanjutan I', 'is_correct' => false],
                    ['huruf' => 'C', 'teks' => 'Pembinaan Tahap Lanjutan II', 'is_correct' => false],
                    ['huruf' => 'D', 'teks' => 'Pembinaan Tahap Akhir (Integrasi)', 'is_correct' => true],
                ],
            ],
        ];

        foreach ($soals as $s) {
            $pilihan = $s['pilihan'];
            unset($s['pilihan']);
            $soal = Soal::firstOrCreate(['pertanyaan' => $s['pertanyaan'], 'materi_id' => $s['materi_id']], $s);
            if ($soal->pilihanJawaban()->count() === 0) {
                foreach ($pilihan as $p) {
                    $soal->pilihanJawaban()->create($p);
                }
            }
        }
    }
}
