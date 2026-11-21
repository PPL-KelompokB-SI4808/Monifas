<?php

namespace Database\Seeders;

use App\Models\FacilityCategory;
use Illuminate\Database\Seeder;

class FacilityCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'code' => 'FC-LISTRIK',
                'name' => 'Instalasi Listrik',
                'description' => 'Semua fasilitas dan perangkat yang berkaitan dengan sistem kelistrikan gedung, termasuk panel, kabel, dan stop kontak.',
                'is_active' => true,
            ],
            [
                'code' => 'FC-PLUMBING',
                'name' => 'Plumbing & Sanitasi',
                'description' => 'Fasilitas perpipaan, drainase, toilet, dan sistem sanitasi gedung.',
                'is_active' => true,
            ],
            [
                'code' => 'FC-HVAC',
                'name' => 'AC & Ventilasi',
                'description' => 'Perangkat pendingin ruangan, exhaust fan, dan sistem ventilasi udara.',
                'is_active' => true,
            ],
            [
                'code' => 'FC-STRUKTUR',
                'name' => 'Struktur Bangunan',
                'description' => 'Komponen struktural gedung seperti atap, dinding, lantai, plafon, dan pondasi.',
                'is_active' => true,
            ],
            [
                'code' => 'FC-JARINGAN',
                'name' => 'Jaringan & Telekomunikasi',
                'description' => 'Infrastruktur jaringan internet, telepon, dan sistem komunikasi internal gedung.',
                'is_active' => true,
            ],
            [
                'code' => 'FC-ELEVATOR',
                'name' => 'Elevator & Eskalator',
                'description' => 'Lift penumpang, eskalator, dan perangkat transportasi vertikal lainnya.',
                'is_active' => true,
            ],
            [
                'code' => 'FC-KEAMANAN',
                'name' => 'Sistem Keamanan',
                'description' => 'CCTV, akses kontrol, alarm kebakaran, dan perangkat keamanan gedung lainnya.',
                'is_active' => true,
            ],
            [
                'code' => 'FC-FURNITUR',
                'name' => 'Furnitur & Inventaris',
                'description' => 'Meja, kursi, lemari, papan tulis, proyektor, dan inventaris ruangan lainnya.',
                'is_active' => true,
            ],
            [
                'code' => 'FC-TAMAN',
                'name' => 'Taman & Area Luar',
                'description' => 'Fasilitas area terbuka, taman, parkir, pagar, dan lansekap eksternal gedung.',
                'is_active' => false,
            ],
        ];

        foreach ($categories as $category) {
            FacilityCategory::updateOrCreate(
                ['code' => $category['code']],
                $category
            );
        }
    }
}
