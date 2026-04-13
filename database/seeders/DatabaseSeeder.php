<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // 1️⃣ Seed roles DULU
        $this->call(RoleSeeder::class);

        // 2️⃣ Buat akun KALAB default
        User::factory()->kalab()->create([
            'name'     => 'Kepala Laboratorium',
            'username' => 'kalab001',
            'email'    => 'kalab@labkom.ac.id',
            'password' => 'password',
        ]);

        // 3️⃣ Dummy data untuk development
        User::factory(10)->create();                    // 10 mahasiswa
        User::factory(5)->aslab()->create();            // 5 aslab
        User::factory(3)->dosenPembimbing()->create();  // 3 dosen
    }
}
