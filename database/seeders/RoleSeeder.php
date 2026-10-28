<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Administrator',
                'slug' => UserRole::Admin->value,
                'description' => 'Memiliki hak akses penuh untuk konfigurasi sistem, manajemen pengguna, dan seluruh modul MONIFAS.',
            ],
            [
                'name' => 'Pengguna Fasilitas',
                'slug' => UserRole::FacilityUser->value,
                'description' => 'Pengguna umum yang dapat membuat laporan kerusakan fasilitas dan memantau status tindak lanjutnya.',
            ],
            [
                'name' => 'Teknisi',
                'slug' => UserRole::Technician->value,
                'description' => 'Petugas lapangan yang menangani pekerjaan pemeliharaan, pencatatan material, dan perbaikan aset fasilitas.',
            ],
            [
                'name' => 'Supervisor',
                'slug' => UserRole::Supervisor->value,
                'description' => 'Penyelia yang bertugas memvalidasi laporan, menugaskan teknisi, serta memonitor kepatuhan SLA.',
            ],
            [
                'name' => 'Manajemen',
                'slug' => UserRole::Management->value,
                'description' => 'Pihak manajemen eksekutif yang mengakses laporan rekapitulasi, dashboard analitik, dan performa pemeliharaan.',
            ],
        ];

        foreach ($roles as $roleData) {
            Role::updateOrCreate(
                ['slug' => $roleData['slug']],
                $roleData
            );
        }
    }
}
