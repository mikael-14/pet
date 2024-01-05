<?php

namespace App\Providers;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Navigation\MenuItem;
use Filament\Navigation\UserMenuItem;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;

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
        // Using Vite
        Filament::serving(function () {
            // Filament::registerViteTheme('resources/css/app.css'); //CUSTOM theme
            // Filament::registerStyles([
            //     asset('css/my-styles.css'),
            // ]);
            if (Filament::auth()->user()) {
                Filament::registerUserMenuItems([
                    'account' => MenuItem::make()->url(UserResource::getUrl('edit', ['record' => Filament::auth()->user()->id])),
                    // ...
                ]);
            }
        });
        Gate::define('access-resource-lock', function (User $user) {
            return $user->can('access_lock_resource_user');
        });

    }
}
