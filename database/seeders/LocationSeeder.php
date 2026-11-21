<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lokasi induk (parent)
        $parents = [
            [
                'code' => 'LOC-KMP-A',
                'name' => 'Kampus A',
                'region' => 'Jakarta Selatan',
                'address' => 'Jl. Kampus Raya No. 1, Jakarta Selatan',
                'description' => 'Kampus utama gedung A beserta seluruh fasilitas penunjangnya.',
                'is_active' => true,
            ],
            [
                'code' => 'LOC-KMP-B',
                'name' => 'Kampus B',
                'region' => 'Jakarta Timur',
                'address' => 'Jl. Pendidikan No. 20, Jakarta Timur',
                'description' => 'Kampus cabang gedung B.',
                'is_active' => true,
            ],
        ];

        foreach ($parents as $data) {
            Location::updateOrCreate(
                ['code' => $data['code']],
                array_merge($data, ['parent_id' => null])
            );
        }

        $kampusA = Location::where('code', 'LOC-KMP-A')->first();
        $kampusB = Location::where('code', 'LOC-KMP-B')->first();

        // Lokasi anak (child) Kampus A
        $childrenA = [
            [
                'code' => 'LOC-KMP-A-GDA',
                'name' => 'Gedung A – Lantai 1',
                'region' => 'Jakarta Selatan',
                'address' => 'Jl. Kampus Raya No. 1, Blok A, Lantai 1, Jakarta Selatan',
                'description' => null,
                'is_active' => true,
            ],
            [
                'code' => 'LOC-KMP-A-GDB',
                'name' => 'Gedung A – Lantai 2',
                'region' => 'Jakarta Selatan',
                'address' => 'Jl. Kampus Raya No. 1, Blok A, Lantai 2, Jakarta Selatan',
                'description' => null,
                'is_active' => true,
            ],
            [
                'code' => 'LOC-KMP-A-LAB',
                'name' => 'Laboratorium Komputer',
                'region' => 'Jakarta Selatan',
                'address' => 'Jl. Kampus Raya No. 1, Blok Lab, Jakarta Selatan',
                'description' => 'Laboratorium komputer untuk praktikum mahasiswa.',
                'is_active' => true,
            ],
        ];

        foreach ($childrenA as $data) {
            Location::updateOrCreate(
                ['code' => $data['code']],
                array_merge($data, ['parent_id' => $kampusA->id])
            );
        }

        // Lokasi anak (child) Kampus B
        $childrenB = [
            [
                'code' => 'LOC-KMP-B-GDA',
                'name' => 'Gedung B – Lantai 1',
                'region' => 'Jakarta Timur',
                'address' => 'Jl. Pendidikan No. 20, Blok A, Lantai 1, Jakarta Timur',
                'description' => null,
                'is_active' => true,
            ],
            [
                'code' => 'LOC-KMP-B-AULA',
                'name' => 'Aula Serbaguna',
                'region' => 'Jakarta Timur',
                'address' => 'Jl. Pendidikan No. 20, Aula, Jakarta Timur',
                'description' => 'Aula untuk kegiatan besar dan seminar.',
                'is_active' => false,
            ],
        ];

        foreach ($childrenB as $data) {
            Location::updateOrCreate(
                ['code' => $data['code']],
                array_merge($data, ['parent_id' => $kampusB->id])
            );
        }
    }
}
