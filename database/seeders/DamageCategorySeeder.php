<?php

namespace Database\Seeders;

use App\Models\DamageCategory;
use Illuminate\Database\Seeder;

class DamageCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'code' => 'DC-RUSAK-BERAT',
                'name' => 'Kerusakan Berat',
                'severity_hint' => 'critical',
                'description' => 'Kerusakan parah yang mengakibatkan fasilitas tidak dapat digunakan sama sekali dan berpotensi membahayakan keselamatan.',
                'is_active' => true,
            ],
            [
                'code' => 'DC-RUSAK-SEDANG',
                'name' => 'Kerusakan Sedang',
                'severity_hint' => 'major',
                'description' => 'Kerusakan yang mengurangi fungsi fasilitas secara signifikan namun masih dapat digunakan dengan keterbatasan.',
                'is_active' => true,
            ],
            [
                'code' => 'DC-RUSAK-RINGAN',
                'name' => 'Kerusakan Ringan',
                'severity_hint' => 'minor',
                'description' => 'Kerusakan kecil yang tidak mengganggu fungsi utama fasilitas, bersifat kosmetik atau perawatan rutin.',
                'is_active' => true,
            ],
            [
                'code' => 'DC-MATI-TOTAL',
                'name' => 'Mati / Tidak Berfungsi',
                'severity_hint' => 'critical',
                'description' => 'Fasilitas atau perangkat sama sekali tidak menyala atau tidak merespons, memerlukan penanganan segera.',
                'is_active' => true,
            ],
            [
                'code' => 'DC-BOCOR',
                'name' => 'Kebocoran',
                'severity_hint' => 'major',
                'description' => 'Kebocoran air, gas, atau cairan lainnya yang dapat merusak area sekitar dan menimbulkan risiko tambahan.',
                'is_active' => true,
            ],
            [
                'code' => 'DC-KORSLETING',
                'name' => 'Korsleting Listrik',
                'severity_hint' => 'critical',
                'description' => 'Hubungan pendek atau gangguan listrik yang berpotensi menyebabkan kebakaran atau kecelakaan.',
                'is_active' => true,
            ],
            [
                'code' => 'DC-RETAK',
                'name' => 'Retak / Pecah',
                'severity_hint' => 'major',
                'description' => 'Keretakan atau kerusakan fisik pada komponen struktur, kaca, dinding, atau lantai.',
                'is_active' => true,
            ],
            [
                'code' => 'DC-KOTOR',
                'name' => 'Kotor / Vandalisme',
                'severity_hint' => 'minor',
                'description' => 'Kondisi kotor, coretan, atau vandalisme yang mempengaruhi estetika dan kebersihan fasilitas.',
                'is_active' => true,
            ],
            [
                'code' => 'DC-MACET',
                'name' => 'Macet / Tersumbat',
                'severity_hint' => 'major',
                'description' => 'Fasilitas mekanis atau saluran yang macet, tersumbat, atau tidak dapat bergerak sebagaimana mestinya.',
                'is_active' => true,
            ],
            [
                'code' => 'DC-LAINNYA',
                'name' => 'Lainnya',
                'severity_hint' => null,
                'description' => 'Jenis kerusakan yang tidak termasuk dalam kategori yang tersedia di atas.',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            DamageCategory::updateOrCreate(
                ['code' => $category['code']],
                $category
            );
        }
    }
}
