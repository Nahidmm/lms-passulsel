<?php

namespace Database\Seeders;

use App\Models\Materi;
use App\Models\Soal;
use App\Models\PilihanJawaban;
use App\Models\TopikPelatihan;
use Illuminate\Database\Seeder;

class SamplePretestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Pastikan ada topik pelatihan untuk evaluasi pretest
        $topikUmum = TopikPelatihan::firstOrCreate(
            ['nama_topik' => 'Pengetahuan Umum Pemasyarakatan']
        );
        
        $topikTeknis = TopikPelatihan::firstOrCreate(
            ['nama_topik' => 'Teknis Pengamanan']
        );

        // 2. Hapus pretest lama jika ada, atau buat yang baru
        $pretestLama = Materi::where('is_pretest', true)->first();
        if ($pretestLama) {
            $pretestLama->soals()->delete();
            $pretestLama->delete();
        }

        // 3. Buat Materi Pretest Interaktif
        $pretest = Materi::create([
            'pelatihan_id' => null, // Standalone pretest
            'judul' => 'Pretest Kompetensi Awal: Quest Mode',
            'deskripsi' => 'Mari kita ukur pemahaman awal Anda dengan kuis interaktif ini.',
            'jenis' => 'quiz',
            'file_path' => null,
            'url_link' => null,
            'durasi_menit' => 15,
            'is_pretest' => true,
            'is_active' => true,
            'poin' => 100,
            'passing_grade' => 60,
            'mode_tampilan' => 'interaktif', // Game theme
            'sub_mode' => 'standard',
            'timer_per_soal' => 60,
            'acak_soal' => true,
            'show_answer_review' => true,
            'max_attempts' => 1,
            'strict_anti_cheat' => true,
            'urutan' => 0
        ]);

        // ==========================================
        // SOAL 1: Pilihan Ganda (Tebak Definisi)
        // ==========================================
        $soal1 = Soal::create([
            'topik_pelatihan_id' => $topikUmum->id,
            'pertanyaan' => "Apa kepanjangan dari LAPAS?",
            'tipe' => 'pilihan_ganda',
            'pembahasan' => 'LAPAS singkatan dari Lembaga Pemasyarakatan.',
            'bobot' => 20,
            'is_active' => true,
        ]);
        $pretest->soals()->attach($soal1->id);
        PilihanJawaban::insert([
            ['soal_id' => $soal1->id, 'huruf' => 'A', 'teks' => 'Lembaga Pemasyarakatan', 'is_correct' => true],
            ['soal_id' => $soal1->id, 'huruf' => 'B', 'teks' => 'Lembaga Pendidikan', 'is_correct' => false],
            ['soal_id' => $soal1->id, 'huruf' => 'C', 'teks' => 'Layanan Pemasyarakatan', 'is_correct' => false],
            ['soal_id' => $soal1->id, 'huruf' => 'D', 'teks' => 'Lembaga Pengawasan', 'is_correct' => false],
        ]);

        // ==========================================
        // SOAL 2: Multi Select (Pilih Beberapa)
        // ==========================================
        $soal2 = Soal::create([
            'topik_pelatihan_id' => $topikTeknis->id,
            'pertanyaan' => "Pilih semua tindakan yang merupakan bagian dari SOP penggeledahan blok hunian (Multi-select):",
            'tipe' => 'multi_select',
            'pembahasan' => 'Tindakan penggeledahan harus diawasi komandan jaga, dilakukan teliti, dan barang temuan dicatat. Tidak boleh merusak barang tanpa alasan.',
            'bobot' => 30,
            'is_active' => true,
        ]);
        $pretest->soals()->attach($soal2->id);
        PilihanJawaban::insert([
            ['soal_id' => $soal2->id, 'huruf' => 'A', 'teks' => 'Meminta izin dan mengawasi WBP dari luar kamar', 'is_correct' => true],
            ['soal_id' => $soal2->id, 'huruf' => 'B', 'teks' => 'Mencatat dan mengamankan barang terlarang', 'is_correct' => true],
            ['soal_id' => $soal2->id, 'huruf' => 'C', 'teks' => 'Membuang makanan WBP tanpa pengecekan', 'is_correct' => false],
            ['soal_id' => $soal2->id, 'huruf' => 'D', 'teks' => 'Melakukan penggeledahan badan sebelum WBP keluar blok', 'is_correct' => true],
        ]);

        // ==========================================
        // SOAL 3: Isian Singkat
        // ==========================================
        $soal3 = Soal::create([
            'topik_pelatihan_id' => $topikUmum->id,
            'pertanyaan' => "Ketik jawaban satu kata:\nSistem Pemasyarakatan di Indonesia secara resmi digagas pertama kali pada tahun 1964 oleh tokoh yang sering disebut sebagai Bapak Pemasyarakatan, yaitu Bapak Sahardjo. Institusi yang membawahi Pemasyarakatan adalah Kementerian Hukum dan...?",
            'tipe' => 'isian_singkat',
            'pembahasan' => 'Kementerian Hukum dan HAM (atau Hak Asasi Manusia). Dalam struktur saat ini disebut Kemenimipas / Kemenkumham.',
            'bobot' => 20,
            'is_active' => true,
        ]);
        $pretest->soals()->attach($soal3->id);
        // Untuk isian singkat, teks pertama = kunci jawaban
        PilihanJawaban::insert([
            ['soal_id' => $soal3->id, 'huruf' => '', 'teks' => 'HAM', 'is_correct' => true],
        ]);

        // ==========================================
        // SOAL 4: Menjodohkan
        // ==========================================
        $soal4 = Soal::create([
            'topik_pelatihan_id' => $topikTeknis->id,
            'pertanyaan' => "Jodohkan istilah berikut dengan kepanjangannya (Ketik kepanjangannya di kotak kosong):",
            'tipe' => 'menjodohkan',
            'pembahasan' => 'RUTAN = Rumah Tahanan Negara, BAPAS = Balai Pemasyarakatan',
            'bobot' => 30,
            'is_active' => true,
        ]);
        $pretest->soals()->attach($soal4->id);
        // Teks format: "Bagian Kiri|||Bagian Kanan"
        PilihanJawaban::insert([
            ['soal_id' => $soal4->id, 'huruf' => '', 'teks' => 'RUTAN|||Rumah Tahanan Negara', 'is_correct' => true],
            ['soal_id' => $soal4->id, 'huruf' => '', 'teks' => 'BAPAS|||Balai Pemasyarakatan', 'is_correct' => true],
            ['soal_id' => $soal4->id, 'huruf' => '', 'teks' => 'RUPBASAN|||Rumah Penyimpanan Benda Sitaan Negara', 'is_correct' => true],
        ]);

        $this->command->info("Pretest Interaktif (Sample) berhasil ditambahkan dengan berbagai tipe soal.");
    }
}
