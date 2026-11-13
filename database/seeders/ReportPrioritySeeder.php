<?php

namespace Database\Seeders;

use App\Enums\PriorityLevel;
use App\Models\ReportPriority;
use Illuminate\Database\Seeder;

class ReportPrioritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $priorities = [
            [
                'name' => 'Darurat',
                'level' => PriorityLevel::Emergency->value,
                'sla_response_hours' => 1,
                'sla_resolution_hours' => 4,
                'badge_variant' => 'danger',
                'sort_order' => 1,
                'description' => 'Kerusakan kritis yang membahayakan keselamatan atau menghentikan operasional vital kampus.',
                'is_active' => true,
            ],
            [
                'name' => 'Tinggi',
                'level' => PriorityLevel::High->value,
                'sla_response_hours' => 4,
                'sla_resolution_hours' => 24,
                'badge_variant' => 'warning',
                'sort_order' => 2,
                'description' => 'Kerusakan signifikan yang menghambat kegiatan operasional utama atau menimbulkan risiko lanjutan.',
                'is_active' => true,
            ],
            [
                'name' => 'Sedang',
                'level' => PriorityLevel::Medium->value,
                'sla_response_hours' => 12,
                'sla_resolution_hours' => 48,
                'badge_variant' => 'info',
                'sort_order' => 3,
                'description' => 'Kerusakan fasilitas umum yang tidak langsung melumpuhkan aktivitas namun perlu penanganan standar.',
                'is_active' => true,
            ],
            [
                'name' => 'Rendah',
                'level' => PriorityLevel::Low->value,
                'sla_response_hours' => 24,
                'sla_resolution_hours' => 72,
                'badge_variant' => 'secondary',
                'sort_order' => 4,
                'description' => 'Kerusakan kosmetik, pemeliharaan preventif, atau gangguan minor dengan dampak rendah.',
                'is_active' => true,
            ],
        ];

        foreach ($priorities as $priority) {
            ReportPriority::updateOrCreate(
                ['level' => $priority['level']],
                $priority
            );
        }
    }
}
