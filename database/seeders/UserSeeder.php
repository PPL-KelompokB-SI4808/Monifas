<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::where('slug', UserRole::Admin->value)->first();
        $facilityUserRole = Role::where('slug', UserRole::FacilityUser->value)->first();
        $technicianRole = Role::where('slug', UserRole::Technician->value)->first();
        $supervisorRole = Role::where('slug', UserRole::Supervisor->value)->first();
        $managementRole = Role::where('slug', UserRole::Management->value)->first();

        $users = [
            [
                'name' => 'Administrator',
                'email' => 'admin@monifas.id',
                'password' => Hash::make('password'),
                'role_id' => $adminRole?->id,
                'is_active' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Pengguna Fasilitas',
                'email' => 'user@monifas.id',
                'password' => Hash::make('password'),
                'role_id' => $facilityUserRole?->id,
                'is_active' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Teknisi Lapangan',
                'email' => 'technician@monifas.id',
                'password' => Hash::make('password'),
                'role_id' => $technicianRole?->id,
                'is_active' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Supervisor Operasional',
                'email' => 'supervisor@monifas.id',
                'password' => Hash::make('password'),
                'role_id' => $supervisorRole?->id,
                'is_active' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Manajemen Eksekutif',
                'email' => 'management@monifas.id',
                'password' => Hash::make('password'),
                'role_id' => $managementRole?->id,
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}
