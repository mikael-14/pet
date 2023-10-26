<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Broadcasting
    |--------------------------------------------------------------------------
    |
    | By uncommenting the Laravel Echo configuration, you may connect Filament
    | to any Pusher-compatible websockets server.
    |
    | This will allow your users to receive real-time notifications.
    |
    */

    'broadcasting' => [

        // 'echo' => [
        //     'broadcaster' => 'pusher',
        //     'key' => env('VITE_PUSHER_APP_KEY'),
        //     'cluster' => env('VITE_PUSHER_APP_CLUSTER'),
        //     'forceTLS' => true,
        // ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | This is the storage disk Filament will use to put media. You may use any
    | of the disks defined in the `config/filesystems.php`.
    |
    */

    'default_filesystem_disk' => env('FILAMENT_FILESYSTEM_DISK', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Google Fonts
    |--------------------------------------------------------------------------
    |
    | This is the URL for Google Fonts that should be loaded. You may use any
    | font, or set to `null` to prevent any Google Fonts from loading.
    |
    | When using a custom font, you should also set the font family in your
    | custom theme's `tailwind.config.js` file.
    |
    */

    'google_fonts' => 'https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,400;0,500;0,700;1,400;1,500;1,700&display=swap',

    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    |
    | You may customize the middleware stack that Filament uses to handle
    | requests.
    |
    */

    'middleware' => [
        'auth' => [
            RedirectFilament::class, //custom request handler to redirect the user case of no access 
            ChangeLanguageLocale::class, //custom request handler to redirect the user case of no access 
            Authenticate::class,
        ],
        'base' => [
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            AuthenticateSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
            DispatchServingFilamentEvent::class,
            MirrorConfigToSubpackages::class,
        ],
    ],
    /*
    |--------------------------------------------------------------------------
    | Filament Date Format
    |--------------------------------------------------------------------------
    |
    | The default format for date inputs and displays
    |
    */
    'date_format' => 'd/m/Y',
    /*
    |--------------------------------------------------------------------------
    | Filament Date Format
    |--------------------------------------------------------------------------
    |
    | The default format for date time inputs and displays
    |
    */
    'date_time_format' => 'd/m/Y H:i',
];