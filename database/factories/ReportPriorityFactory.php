<?php

namespace Database\Factories;

use App\Enums\PriorityLevel;
use App\Models\ReportPriority;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ReportPriority>
 */
class ReportPriorityFactory extends Factory
{
    protected $model = ReportPriority::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        /** @var PriorityLevel $level */
        $level = fake()->randomElement(PriorityLevel::cases());

        return [
            'name' => 'Prioritas '.$level->label(),
            'level' => $level->value,
            'sla_response_hours' => match ($level) {
                PriorityLevel::Emergency => 1,
                PriorityLevel::High => 4,
                PriorityLevel::Medium => 12,
                PriorityLevel::Low => 24,
            },
            'sla_resolution_hours' => match ($level) {
                PriorityLevel::Emergency => 4,
                PriorityLevel::High => 24,
                PriorityLevel::Medium => 48,
                PriorityLevel::Low => 72,
            },
            'badge_variant' => $level->badgeVariant(),
            'sort_order' => $level->defaultSortOrder(),
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }

    /**
     * State for Emergency priority.
     */
    public function emergency(): static
    {
        return $this->state(fn () => [
            'name' => 'Darurat',
            'level' => PriorityLevel::Emergency->value,
            'sla_response_hours' => 1,
            'sla_resolution_hours' => 4,
            'badge_variant' => 'danger',
            'sort_order' => 1,
            'description' => 'Kerusakan kritis yang membahayakan keselamatan atau menghentikan operasional vital.',
            'is_active' => true,
        ]);
    }

    /**
     * State for High priority.
     */
    public function high(): static
    {
        return $this->state(fn () => [
            'name' => 'Tinggi',
            'level' => PriorityLevel::High->value,
            'sla_response_hours' => 4,
            'sla_resolution_hours' => 24,
            'badge_variant' => 'warning',
            'sort_order' => 2,
            'description' => 'Kerusakan signifikan yang mengganggu aktivitas utama pengguna fasilitas.',
            'is_active' => true,
        ]);
    }

    /**
     * State for Medium priority.
     */
    public function medium(): static
    {
        return $this->state(fn () => [
            'name' => 'Sedang',
            'level' => PriorityLevel::Medium->value,
            'sla_response_hours' => 12,
            'sla_resolution_hours' => 48,
            'badge_variant' => 'info',
            'sort_order' => 3,
            'description' => 'Kerusakan fasilitas standar yang tidak menimbulkan dampak mendesak.',
            'is_active' => true,
        ]);
    }

    /**
     * State for Low priority.
     */
    public function low(): static
    {
        return $this->state(fn () => [
            'name' => 'Rendah',
            'level' => PriorityLevel::Low->value,
            'sla_response_hours' => 24,
            'sla_resolution_hours' => 72,
            'badge_variant' => 'secondary',
            'sort_order' => 4,
            'description' => 'Kerusakan kosmetik atau pemeliharaan berkala non-urgensi.',
            'is_active' => true,
        ]);
    }
}
