<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectFilament
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        // Add MustVerifyEmail and hasVerifiedEmail to check if user has email verified
        /*
        if ($user instanceof FilamentUser && $user instanceof MustVerifyEmail) {
            if (! $user->canAccessFilament() && ! $user->hasVerifiedEmail()) {
                return redirect()->route('verification.notice');
            }
        }
        */
        // see more at https://filamentphp.com/tricks/redirect-in-case-canaccessfilament-fails
        if ($user instanceof FilamentUser ){ //} && $user instanceof MustVerifyEmail) {
            if (! $user->canAccessPanel()){ //} && ! $user->hasVerifiedEmail()) {
                return redirect()->route('admin.verification.notice');
            }
        }
     
        return $next($request);
    }
}
