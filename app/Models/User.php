<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
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

    // --- ASIGNACIÓN AUTOMÁTICA DE ROL ---
    protected static function booted(): void
    {
        static::created(function ($user) {
            // Asignamos rol por defecto al registrarse
            if ($user->roles()->count() === 0) {
                $user->assignRole('Técnico');
            }
        });
    }

    /**
     * CONTROL DE ACCESO AL PANEL
     * Aquí es donde evitamos el 403 y controlamos quién entra.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // 1. Si es Admin, entra siempre.
        if ($this->hasRole('Admin')) {
            return true;
        }

        // 2. IMPORTANTE: Si es Técnico o Director, SOLO entra si ya tiene Dirección asignada por el Admin.
        // Si direccion_id es null, Filament le denegará el acceso (ideal para tu flujo de aprobación).
        if ($panel->getId() === 'admin') {
            return $this->hasRole(['Director']);
        }

        // 3. Regla para el Panel de Técnico (donde entran los Técnicos)
        if ($panel->getId() === 'tecnico') {
            return $this->hasRole('Técnico');
        }

        return false;
    }

    public function getFilamentName(): string
    {
        return "{$this->nombre} {$this->apellido}";
    }

    // Relaciones
    public function direccion()
    {
        return $this->belongsTo(Direccion::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }
}
