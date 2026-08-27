<?php

namespace App\Imports;

use App\Models\User;
use App\Models\UnitKerja;
use App\Models\Jabatan;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class UsersImport implements ToModel, WithHeadingRow, SkipsEmptyRows
{
    public function model(array $row)
    {
        // Skip if NIP is empty
        if (empty($row['nip'])) {
            return null;
        }

        // Skip if User already exists
        if (User::where('nip', $row['nip'])->exists()) {
            return null;
        }

        // Find or Create Unit Kerja
        $unitKerjaName = trim($row['unit_kerja'] ?? '');
        $unitKerjaId = null;
        if (!empty($unitKerjaName)) {
            $unitKerja = UnitKerja::firstOrCreate(['nama_unit' => $unitKerjaName]);
            $unitKerjaId = $unitKerja->id;
        }

        // Find Jabatan, if not exist then reject (skip row)
        $jabatanName = trim($row['jabatan'] ?? '');
        $jabatanId = null;
        if (!empty($jabatanName)) {
            $jabatan = Jabatan::where('nama_jabatan', $jabatanName)->first();
            if (!$jabatan) {
                // Reject if not found
                return null;
            }
            $jabatanId = $jabatan->id;
        }

        return new User([
            'nip' => $row['nip'],
            'nama' => $row['nama_lengkap'],
            'unit_kerja_id' => $unitKerjaId,
            'golongan' => $row['pangkat_golongan'] ?? null,
            'jabatan_id' => $jabatanId,
            'alamat' => $row['alamat'] ?? null,
            'no_hp' => $row['no_hp'] ?? null,
            'email' => $row['email'] ?? null,
            'password' => Hash::make('password123'), // Default password
            'force_change_password' => true,
            'status_akun' => 'approved',
            'role' => 'peserta'
        ]);
    }
}
