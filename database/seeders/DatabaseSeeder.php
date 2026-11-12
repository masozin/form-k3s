<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use App\Models\School;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::create([
            'npsn' => '12345678',
            'name' => 'SMA Negeri 1 Bandar Lampung',
            'address' => 'Jl. Contoh No. 1, Bandar Lampung',
            'level' => 'SMA',
            'accreditation' => 'A',
            'principal_name' => 'Drs. Contoh Kepsek',
            'principal_nip' => '196501011990031001',
        ]);

        User::create([
            'name' => 'Admin Utama',
            'email' => 'admin@k3s.test',
            'password' => bcrypt('password'),
            'school_id' => $school->id,
            'role' => User::ROLE_ADMIN,
        ]);

        User::create([
            'name' => 'Operator Sekolah',
            'email' => 'operator@k3s.test',
            'password' => bcrypt('password'),
            'school_id' => $school->id,
            'role' => User::ROLE_OPERATOR,
        ]);

        User::create([
            'name' => 'Pengguna Biasa',
            'email' => 'user@k3s.test',
            'password' => bcrypt('password'),
            'school_id' => $school->id,
            'role' => User::ROLE_USER,
        ]);
    }
}
