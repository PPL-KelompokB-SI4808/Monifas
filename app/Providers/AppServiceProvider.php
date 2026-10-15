<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::anonymousComponentPath(resource_path('views/pages/home/components'), 'home');

        $modules = [
            'users-and-access' => ['users', 'roles', 'user-addresses'],
            'facility' => ['buildings', 'rooms', 'facilities'],
            'location-and-category' => ['locations', 'facility-categories', 'damage-categories'],
            'reporting' => ['reports', 'report-evidence', 'report-priorities'],
            'maintenance' => ['officers', 'assignments', 'schedules'],
            'repair' => ['repairs', 'materials', 'costs'],
            'monitoring-and-analytic' => ['dashboard', 'maintenance-report', 'sla-monitoring'],
            'supporting' => ['announcements', 'feedback', 'campaigns'],
        ];

        foreach ($modules as $module => $submodules) {
            foreach ($submodules as $submodule) {
                $componentsPath = resource_path("views/pages/dashboard/{$module}/{$submodule}/components");
                Blade::anonymousComponentPath($componentsPath, "{$module}.{$submodule}");
                Blade::anonymousComponentPath($componentsPath, $submodule);
            }
        }
    }
}
