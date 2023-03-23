<?php

namespace App\Providers;

use App\Filament\Resources\UserResource;
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
                    'account' => UserMenuItem::make()->url(UserResource::getUrl('edit', Filament::auth()->user()->id )),
                    // ...
                ]);
            }
            });
        
    }
}
