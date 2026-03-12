<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Spatie\Permission\Traits\HasRoles; // <-- 1. Agrega esta importación



class User extends Authenticatable implements FilamentUser, HasName
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles; // <-- 2. Agrega HasRoles aquí

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nombre',
        'apellido',
        'usuario',
        'email',
        'password',
        'celular',
        'direccion_id',
        'area_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
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
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // Esto verifica que solo los usuarios con rol 'Admin' puedan entrar
        return $this->hasRole('Admin');
    }

    public function getFilamentName(): string
    {
        // Esto mostrará "Guillermo Admin" en la esquina superior
        return "{$this->nombre} {$this->apellido}";
    }

    // Relación: Un usuario pertenece a una Dirección
    public function direccion()
    {
        return $this->belongsTo(Direccion::class);
    }

    // Relación: Un usuario pertenece a un Área
    public function area()
    {
        return $this->belongsTo(Area::class);
    }
}
