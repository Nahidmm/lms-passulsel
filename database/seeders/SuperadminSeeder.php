<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperadminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['nip' => '000000000000000001'],
            [
                'nama' => 'Super Administrator',
                'email' => 'superadmin@lms-passulsel.id',
                'golongan' => null,
                'jabatan_id' => null,
                'role' => 'superadmin',
                'status_akun' => 'approved',
                'password' => Hash::make('superadmin2026'),
                'force_change_password' => false,
            ]
        );
    }
}
