<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Register as BaseRegister;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use App\Models\Direccion;
use App\Models\Area;

class RegistroTecnico extends BaseRegister
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Nombre de Usuario (Obligatorio y Único)
                TextInput::make('usuario')
                    ->label('Nombre de Usuario')
                    ->required()
                    ->unique('users', 'usuario')
                    ->maxLength(255),

                // Nombre (Obligatorio)
                TextInput::make('nombre')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),

                // Apellido (Obligatorio)
                TextInput::make('apellido')
                    ->label('Apellido')
                    ->required()
                    ->maxLength(255),

                // Email (Obligatorio)
                $this->getEmailFormComponent()
                    ->label('Correo Electrónico'),

                // Contraseña (Obligatoria)
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),

                // Estructura CFI (Obligatorios)
                Select::make('direccion_id')
                    ->label('Dirección')
                    ->options(Direccion::all()->pluck('direccion', 'id')) // Trae el nombre y el ID
                    ->required()
                    ->searchable()
                    ->native(false) // Hace que se vea más lindo con el estilo de Filament
                    ->preload(),
                    
                Select::make('area_id')
                    ->label('Área')
                    ->options(Area::all()->pluck('area', 'id'))
                    ->required()
                    ->searchable()
                    ->native(false)
                    ->preload(),
            ]);
    }
    protected function handleRegistration(array $data): \Illuminate\Database\Eloquent\Model
    {
        // 1. Encriptamos la contraseña
        $data['password'] = \Illuminate\Support\Facades\Hash::make($data['password']);
        
        // 2. Creamos el usuario y lo guardamos en una variable
        $user = \App\Models\User::create($data);

        // 3. ¡EL PASO MÁGICO! Le asignamos el rol inmediatamente
        // OJO: Escribí 'Tecnico' o 'Técnico' exactamente igual a como figura en tu tabla 'roles'
        $user->assignRole('Técnico');

        // 4. Devolvemos el usuario ya con su rol puesto
        return $user;
    }
}