<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * By default, only Filament admins can impersonate other users. 
     * You can control this by adding a 'canImpersonate' method to your FilamentUser class
     */
    public function canImpersonate()
    {
        if($this->hasRole('super_admin'))
        {
            return true;
        } 
        return false;
    }
    /**
     * You can also control which targets can be impersonated.
     * Just add a 'canBeImpersonated' method to the user class with whatever logic you need
     */
    public function canBeImpersonated()
    {
        // Let's prevent impersonating other users that are super admins
        if(!$this->hasRole('super_admin'))
        {
            return true;
        } 
        return false;
    }
}
