<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Akun Pengguna LMS (1 Dosen & 1 Mahasiswa)
        // Dosen pengampu (email sebagai identitas akun)
        User::updateOrCreate(
            ['email' => 'budi.santoso@celoe.test'],
            [
                'name' => 'Dr. Ir. Budi Santoso, M.T.',
                'password' => Hash::make('password123'),
                'role' => 'dosen',
                'nomor_induk' => '198501012010041001',
            ],
        );

        // Mahasiswa
        User::updateOrCreate(
            ['email' => 'syaddad@student.celoe.test'],
            [
                'name' => 'Syaddad Raihan Putra',
                'password' => Hash::make('password123'),
                'role' => 'mahasiswa',
                'nomor_induk' => '102042500079',
            ],
        );
    }
}
