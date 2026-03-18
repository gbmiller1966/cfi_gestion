<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasName
{
    use HasFactory, Notifiable, HasRoles;

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

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // --- MAGIA 1: ASIGNACIÓN AUTOMÁTICA DE ROL ---
    protected static function booted(): void
    {
        static::created(function ($user) {
            // Si el usuario se acaba de registrar y no tiene roles, le clavamos "Técnico"
            if ($user->roles()->count() === 0) {
                $user->assignRole('Técnico');
            }
        });
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // --- MAGIA 2: EL DIRECTOR ENTRA POR LA PUERTA PRINCIPAL ---
        if ($panel->getId() === 'admin') {
            return $this->hasAnyRole(['Admin', 'Director']);
        }

        if ($panel->getId() === 'tecnico') {
            return $this->hasAnyRole(['Técnico', 'Tecnico', 'tecnico']);
        }

        return false;
    }

    public function getFilamentName(): string
    {
        return "{$this->nombre} {$this->apellido}";
    }

    public function direccion()
    {
        return $this->belongsTo(Direccion::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }
}
