<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChangeLanguageLocale
{
        /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $locale = session()->get('locale') ?? $request->get('locale') ?? (Auth::user() ? Auth::user()->locale : null) ?? config('app.locale', 'en');

        if (in_array($locale, config('filament-spatie-laravel-translatable-plugin.default_locales'))) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
