<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name'            => 'KALAB',
                'slug'            => 'kalab',
                'description'     => 'Kepala Laboratorium — memiliki akses penuh ke seluruh sistem.',
                'dashboard_route' => 'kalab.dashboard',
                'color_class'     => 'from-violet-600 to-violet-800',
                'icon'            => 'shield-check',
                'is_active'       => true,
            ],
            [
                'name'            => 'Mahasiswa',
                'slug'            => 'mahasiswa',
                'description'     => 'Mahasiswa peserta praktikum.',
                'dashboard_route' => 'mahasiswa.dashboard',
                'color_class'     => 'from-sky-500 to-sky-700',
                'icon'            => 'academic-cap',
                'is_active'       => true,
            ],
            [
                'name'            => 'ASLAB',
                'slug'            => 'aslab',
                'description'     => 'Asisten Laboratorium — membantu pelaksanaan praktikum.',
                'dashboard_route' => 'aslab.dashboard',
                'color_class'     => 'from-emerald-500 to-emerald-700',
                'icon'            => 'beaker',
                'is_active'       => true,
            ],
            [
                'name'            => 'Dosen Pembimbing',
                'slug'            => 'dosen-pembimbing',
                'description'     => 'Dosen yang membimbing dan menilai praktikum.',
                'dashboard_route' => 'dosen-pembimbing.dashboard',
                'color_class'     => 'from-amber-500 to-amber-700',
                'icon'            => 'briefcase',
                'is_active'       => true,
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['slug' => $role['slug']], $role);
        }

        $this->command->info('✓ 4 Roles berhasil di-seed.');
    }
}