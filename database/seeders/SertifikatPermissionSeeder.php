<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class SertifikatPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $perm = Permission::firstOrCreate(
            ['kode' => 'manage_sertifikat'],
            [
                'nama' => 'Kelola Sertifikat',
                'deskripsi' => 'Akses penuh untuk mengatur konfigurasi template sertifikat kelulusan.',
                'grup' => 'Sistem',
                'urutan' => 99
            ]
        );

        $superadmin = Role::where('nama', 'Superadmin')->first();
        if ($superadmin) {
            $superadmin->permissions()->syncWithoutDetaching([$perm->id]);
        }
    }
}
