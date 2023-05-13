<?php

namespace App\Providers;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Navigation\UserMenuItem;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Using Vite
        Filament::serving(function () {
            Filament::registerViteTheme('resources/css/app.css'); //CUSTOM theme
            // Filament::registerStyles([
            //     asset('css/my-styles.css'),
            // ]);
            if (Filament::auth()->user()) {
                Filament::registerUserMenuItems([
                    'account' => UserMenuItem::make()->url(UserResource::getUrl('edit', Filament::auth()->user()->id)),
                    // ...
                ]);
            }
        });
        Gate::define('access-resource-lock', function (User $user) {
            return $user->can('access_lock_resource_user');
        });
    }
}
