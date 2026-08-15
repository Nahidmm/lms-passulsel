<?php

namespace Database\Seeders;

use App\Models\HariLibur;
use Illuminate\Database\Seeder;

class HariLiburSeeder extends Seeder
{
    public function run(): void
    {
        $liburs = [
            // Libur Nasional 2026
            ['tanggal' => '2026-01-01', 'keterangan' => 'Tahun Baru Masehi', 'jenis' => 'libur_nasional'],
            ['tanggal' => '2026-01-27', 'keterangan' => 'Isra Miraj Nabi Muhammad SAW', 'jenis' => 'libur_nasional'],
            ['tanggal' => '2026-01-29', 'keterangan' => 'Tahun Baru Imlek 2577', 'jenis' => 'libur_nasional'],
            ['tanggal' => '2026-03-03', 'keterangan' => 'Hari Suci Nyepi (Tahun Baru Saka 1948)', 'jenis' => 'libur_nasional'],
            ['tanggal' => '2026-03-20', 'keterangan' => 'Hari Raya Idul Fitri 1447 H', 'jenis' => 'libur_nasional'],
            ['tanggal' => '2026-03-21', 'keterangan' => 'Hari Raya Idul Fitri 1447 H', 'jenis' => 'libur_nasional'],
            ['tanggal' => '2026-04-03', 'keterangan' => 'Wafat Yesus Kristus', 'jenis' => 'libur_nasional'],
            ['tanggal' => '2026-04-05', 'keterangan' => 'Hari Paskah', 'jenis' => 'libur_nasional'],
            ['tanggal' => '2026-05-01', 'keterangan' => 'Hari Buruh Internasional', 'jenis' => 'libur_nasional'],
            ['tanggal' => '2026-05-14', 'keterangan' => 'Kenaikan Yesus Kristus', 'jenis' => 'libur_nasional'],
            ['tanggal' => '2026-05-23', 'keterangan' => 'Hari Raya Waisak 2570 BE', 'jenis' => 'libur_nasional'],
            ['tanggal' => '2026-05-28', 'keterangan' => 'Hari Raya Idul Adha 1447 H', 'jenis' => 'libur_nasional'],
            ['tanggal' => '2026-06-17', 'keterangan' => 'Tahun Baru Islam 1448 H', 'jenis' => 'libur_nasional'],
            ['tanggal' => '2026-08-17', 'keterangan' => 'Hari Kemerdekaan Republik Indonesia', 'jenis' => 'libur_nasional'],
            ['tanggal' => '2026-09-16', 'keterangan' => 'Maulid Nabi Muhammad SAW', 'jenis' => 'libur_nasional'],
            ['tanggal' => '2026-12-25', 'keterangan' => 'Hari Raya Natal', 'jenis' => 'libur_nasional'],
            // Cuti Bersama (estimasi)
            ['tanggal' => '2026-03-17', 'keterangan' => 'Cuti Bersama Idul Fitri', 'jenis' => 'cuti_bersama'],
            ['tanggal' => '2026-03-18', 'keterangan' => 'Cuti Bersama Idul Fitri', 'jenis' => 'cuti_bersama'],
            ['tanggal' => '2026-03-19', 'keterangan' => 'Cuti Bersama Idul Fitri', 'jenis' => 'cuti_bersama'],
            ['tanggal' => '2026-03-23', 'keterangan' => 'Cuti Bersama Idul Fitri', 'jenis' => 'cuti_bersama'],
            ['tanggal' => '2026-03-24', 'keterangan' => 'Cuti Bersama Idul Fitri', 'jenis' => 'cuti_bersama'],
            ['tanggat' => '2026-12-26', 'keterangan' => 'Cuti Bersama Natal', 'jenis' => 'cuti_bersama'],
        ];

        foreach ($liburs as $l) {
            if (isset($l['tanggat'])) {
                $l['tanggal'] = $l['tanggat'];
                unset($l['tanggat']);
            }
            HariLibur::firstOrCreate(['tanggal' => $l['tanggal']], $l);
        }
    }
}
