<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Filament\Navigation\UserMenuItem;
use Illuminate\Support\ServiceProvider;

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
        //load my stylesheet
            Filament::registerStyles([
                asset('css/my-styles.css'),
            ]);
            Filament::serving(function () {
                if (Filament::auth()->user()) {
                Filament::registerUserMenuItems([
                    'account' => UserMenuItem::make()->url('users/' . Filament::auth()->user()->id . '/edit'),
                    // ...
                ]);
            }
            });
        
    }
}
