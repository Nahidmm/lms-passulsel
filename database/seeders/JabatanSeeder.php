<?php

namespace Database\Seeders;

use App\Models\Jabatan;
use Illuminate\Database\Seeder;

class JabatanSeeder extends Seeder
{
    public function run(): void
    {
        $jabatans = [
            [
                'kode_eselon' => 'IV-a',
                'nama_jabatan' => 'Kepala Seksi Bimbingan Narapidana/Anak Didik',
                'tupoksi_deskripsi' => 'Melakukan bimbingan narapidana/anak didik, penyiapan bahan pembinaan, pengawasan, dan penilaian pelaksanaan program pembinaan narapidana/anak didik di Lapas/Rutan.',
            ],
            [
                'kode_eselon' => 'IV-a',
                'nama_jabatan' => 'Kepala Seksi Kegiatan Kerja',
                'tupoksi_deskripsi' => 'Melakukan perencanaan, pelaksanaan, dan pengawasan kegiatan kerja narapidana/anak didik, pengelolaan peralatan, bahan, dan hasil kerja.',
            ],
            [
                'kode_eselon' => 'IV-a',
                'nama_jabatan' => 'Kepala Sub Bagian Tata Usaha',
                'tupoksi_deskripsi' => 'Melakukan urusan tata usaha dan rumah tangga, kepegawaian, keuangan, administrasi umum, dan perlengkapan Lapas/Rutan.',
            ],
            [
                'kode_eselon' => 'IV-a',
                'nama_jabatan' => 'Kepala Seksi Administrasi Keamanan dan Ketertiban',
                'tupoksi_deskripsi' => 'Melakukan penyiapan bahan penegakan disiplin dan tata tertib, pengamanan fisik, keamanan blok/kamar, penggeledahan, penanggulangan gangguan keamanan dan ketertiban.',
            ],
            [
                'kode_eselon' => 'IV-a',
                'nama_jabatan' => 'Kepala Seksi Pelayanan Tahanan',
                'tupoksi_deskripsi' => 'Melakukan pendaftaran dan pencatatan tahanan, pengelolaan barang bawaan tahanan, pemberian hak, kesehatan, dan kebutuhan tahanan.',
            ],
        ];

        foreach ($jabatans as $j) {
            Jabatan::firstOrCreate(['kode_eselon' => $j['kode_eselon'], 'nama_jabatan' => $j['nama_jabatan']], $j);
        }
    }
}
