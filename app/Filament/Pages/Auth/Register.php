<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\Register as BaseRegister;

class Register extends BaseRegister
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Campo Usuario (Columna: usuario)
                TextInput::make('usuario')
                    ->label('Nombre de Usuario')
                    ->required()
                    ->unique('users', 'usuario')
                    ->maxLength(255),

                // Campo Nombre (Columna: nombre)
                TextInput::make('nombre')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),

                // Campo Apellido (Columna: apellido)
                TextInput::make('apellido')
                    ->label('Apellido')
                    ->required()
                    ->maxLength(255),

                // El resto son los originales de Filament ajustados
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ]);
    }

    /**
     * Sobrescribimos este método para que Filament sepa
     * qué datos mandar al crear el modelo User.
     */
    protected function getPreparedFullModelData(array $data): array
    {
        return [
            'usuario'  => $data['usuario'],
            'nombre'   => $data['nombre'],
            'apellido' => $data['apellido'],
            'email'    => $data['email'],
            'password' => $data['password'],
        ];
    }

    protected function getRedirectUrl(): string
    {
        // Una vez registrado, lo mandamos al panel de técnicos
        // que es el que le corresponde por defecto.
        return url('/tecnico');
    }
}
