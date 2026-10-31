<?php

namespace App\Providers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
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
        Gate::define('manage-users', fn (User $user): bool => $user->is_active && $user->hasRole(UserRole::Admin));
        Gate::define('manage-roles', fn (User $user): bool => $user->is_active && $user->hasRole(UserRole::Admin));

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

        Blade::anonymousComponentPath(
            resource_path('views/pages/dashboard/users-and-access/users/components'),
            'users-and-access-users'
        );

        Blade::anonymousComponentPath(
            resource_path('views/pages/dashboard/users-and-access/roles/components'),
            'users-and-access-roles'
        );

        Blade::anonymousComponentPath(
            resource_path('views/pages/dashboard/supporting/announcements/components'),
            'supporting-announcements'
        );

        foreach ($modules as $module => $submodules) {
            foreach ($submodules as $submodule) {
                $componentsPath = resource_path("views/pages/dashboard/{$module}/{$submodule}/components");
                Blade::anonymousComponentPath($componentsPath, "{$module}-{$submodule}");
                Blade::anonymousComponentPath($componentsPath, "{$module}.{$submodule}");
                Blade::anonymousComponentPath($componentsPath, $submodule);
            }
        }
    }
}
