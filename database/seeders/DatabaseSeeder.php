<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role; // <-- Agrega esta importación
use App\Models\User; // <-- Asegúrate de importar el modelo User

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
    // 1. Crear los 4 roles del sistema
            $roleAdmin = Role::create(['name' => 'Admin']);
            $roleTecnico = Role::create(['name' => 'Técnico']);
            $roleJefe = Role::create(['name' => 'Jefe de Área']);
            $roleDirector = Role::create(['name' => 'Director']);

    // 2. Crear tu usuario Administrador
            $adminUser = User::create([
                'nombre' => 'Guillermo',
                'apellido' => 'Admin',
                'usuario' => 'miller',
                'email' => 'admin@cfi.org.ar',
                'password' => Hash::make('password123'),
            ]);

    // 3. Asignarle el rol de Admin a tu usuario
            $adminUser->assignRole($roleAdmin);

            $this->call([
            TablasMaestrasSeeder::class,
            // Acá abajo podrías llamar después a un UserSeeder si querés crear tu usuario y el de Hernán automáticamente
        ]);
    }
}
