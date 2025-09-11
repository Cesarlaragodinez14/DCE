<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;  // Agrega Roles
use Lab404\Impersonate\Models\Impersonate;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;  // Agrega roles
    use Impersonate;

    protected $guard_name = 'web'; // or whatever guard you want to use

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
        'password',
        'firma_autografa',
        'puesto',
        'uaa_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'user_ap_accepted' => 'boolean',
            'user_ap_accepted_date' => 'datetime',
        ];
    }

    public function uaa()
    {
        return $this->belongsTo(CatUaa::class, 'uaa_id');
    }

     /**
     * Por defecto, todos los usuarios pueden impersonar a cualquiera
     * este ejemplo lo limita a solo los admins.
     */
    public function canImpersonate(): bool
    {
        return $this->hasRole('admin'); // Asegúrate de que este método verifica correctamente si el usuario es admin
    }
    
    /**
     * Por defecto, todos los usuarios pueden ser impersonados,
     * esto lo limita a solo ciertos usuarios.
     */
    public function canBeImpersonated(): bool
    {
        return ! $this->hasRole('admin'); // Asegúrate de que los admins no puedan ser impersonados
    }
}
