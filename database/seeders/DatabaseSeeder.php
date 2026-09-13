<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // 1️⃣ Jalankan RoleSeeder terlebih dahulu
        // Ini wajib agar ID role tersedia saat membuat User
        $this->call(RoleSeeder::class);

        // 2️⃣ AKUN KALAB (Kepala Laboratorium)
        User::factory()->kalab()->create([
            'name'     => 'Kepala Laboratorium',
            'username' => 'kalab001',
            'email'    => 'kalab@labkom.ac.id',
            'password' => bcrypt('password'), 
        ]);

        // 3️⃣ AKUN ASLAB (Asisten Laboratorium)
        User::factory()->aslab()->create([
            'name'     => 'Asisten Lab Tester',
            'username' => 'aslab001',
            'email'    => 'aslab@labkom.ac.id',
            'password' => bcrypt('password'),
        ]);

        // 4️⃣ AKUN DOSBIM (Dosen Pembimbing)
        User::factory()->dosenPembimbing()->create([
            'name'     => 'Dosen Pembimbing Tester',
            'username' => 'dosbim001',
            'email'    => 'dosbim@labkom.ac.id',
            'password' => bcrypt('password'),
        ]);

        // 5️⃣ AKUN MAHASISWA (Statis untuk Testing)
        User::factory()->create([
            'name'     => 'Mahasiswa Tester',
            'username' => 'mhs001',
            'email'    => 'mahasiswa@test.com',
            'password' => bcrypt('password123'),
        ]);

        // 6️⃣ DUMMY DATA (Untuk meramaikan sistem)
        // Menghasilkan data acak agar dashboard terlihat penuh saat demo
        User::factory(10)->create();                   // 10 Mahasiswa tambahan
        User::factory(3)->aslab()->create();           // 3 Aslab tambahan
        User::factory(2)->dosenPembimbing()->create(); // 2 Dosen tambahan
    }
}