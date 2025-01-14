<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'isAdmin',
        'signature',
        'terminology_client',
        'terminology_psycho',
        'terminology_behavior',
        'opening_heading',
        'section_breaks',
        'spacing_options',
        'font_style',
        'font_size',
        'date_format',
        'color_opacity',
        'profile_picture',
        'subscription_code',
        'subscription_code_verified',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    protected function profilePicture(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value == null || $value == '' ? null : '/uploads/images/' . $value,
        );
    }
    public function headings()
    {
        return $this->hasMany('App\Models\Heading');
    }
}
