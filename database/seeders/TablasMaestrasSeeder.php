<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TablasMaestrasSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Regiones (No dependen de nadie)
         $regiones = [
            ['id' => 1, 'region' => 'Centro', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'region' => 'Cuyo', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'region' => 'Noreste Argentino', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'region' => 'Noroeste Argentino', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'region' => 'Patagonia', 'created_at' => now(), 'updated_at' => now()],
            // Agregá el resto...
        ];
        DB::table('regiones')->insert($regiones);

        // 2. Provincias (Dependen de regiones)
        $provincias = [
            ['id' => 1, 'region_id' => 1, 'provincia' => 'Buenos Aires', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'region_id' => 4, 'provincia' => 'Catamarca', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'region_id' => 3, 'provincia' => 'Chaco', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'region_id' => 5, 'provincia' => 'Chubut', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'region_id' => 1, 'provincia' => 'Ciudad Autónoma de Buenos Aires', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'region_id' => 1, 'provincia' => 'Córdoba', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'region_id' => 3, 'provincia' => 'Corrientes', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'region_id' => 2, 'provincia' => 'Entre Ríos', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9, 'region_id' => 3, 'provincia' => 'Formosa', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 10, 'region_id' => 4, 'provincia' => 'Jujuy', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 11, 'region_id' => 5, 'provincia' => 'La Pampa', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 12, 'region_id' => 2, 'provincia' => 'La Rioja', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 13, 'region_id' => 2, 'provincia' => 'Mendoza', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 14, 'region_id' => 3, 'provincia' => 'Misiones', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 15, 'region_id' => 5, 'provincia' => 'Neuquén', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 16, 'region_id' => 5, 'provincia' => 'Río Negro', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 17, 'region_id' => 4, 'provincia' => 'Salta', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 18, 'region_id' => 2, 'provincia' => 'San Juan', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 19, 'region_id' => 2, 'provincia' => 'San Luis', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 20, 'region_id' => 5, 'provincia' => 'Santa Cruz', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 21, 'region_id' => 1, 'provincia' => 'Santa Fe', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 22, 'region_id' => 4, 'provincia' => 'Santiago del Estero', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 23, 'region_id' => 5, 'provincia' => 'Tierra del Fuego', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 24, 'region_id' => 4, 'provincia' => 'Tucumán', 'created_at' => now(), 'updated_at' => now()],
            // Completar las 24...
        ];
        DB::table('provincias')->insert($provincias);

/*        // 3. Localidades (Dependen de provincias)
        // TIP: Como son miles, para probar te sugiero cargar solo un par, o importar un archivo .sql aparte si tenés el padrón completo.
        $localidades = [
            ['id' => 1, 'provincia_id' => 1, 'nombre' => 'Córdoba Capital', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'provincia_id' => 2, 'nombre' => 'Rosario', 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('localidades')->insert($localidades); */

        // 4. Direcciones y Áreas (Asumo que Área depende de Dirección)
        $direcciones = [
            ['id' => 1, 'direccion' => 'Dirección de Coordinación', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'direccion' => 'Dirección de Programas', 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('direcciones')->insert($direcciones);

        $areas = [
            ['id' => 1, 'direccion_id' => 2, 'area' => 'Gestión de Gobierno y Regiones Concertadas', 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('areas')->insert($areas);

        // 5. Tipos de Contrato
        $tipos = [
            ['id' => 1, 'tipo' => 'LO Institución', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'tipo' => 'LO Experto', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'tipo' => 'LO Consultora', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'tipo' => 'LO Fundación', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'tipo' => 'LO Cooperativa', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'tipo' => 'LO Servicios', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'tipo' => 'LO Grupo de Expertos', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'tipo' => 'LO Grupo de Consultoras', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9, 'tipo' => 'LO Convenio', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 10, 'tipo' => 'LO Convenio-Institución', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 11, 'tipo' => 'LO Convenio-Experto', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 12, 'tipo' => 'LO Convenio-Consultora', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 13, 'tipo' => 'LO Convenio-Fundación', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 14, 'tipo' => 'Pago por factura', 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('tipos')->insert($tipos);

        // 6. Temas Estratégicos
        $temas = [
            ['id' => 1, 'tema' => 'Análisis y relevamiento económico', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'tema' => 'Educación', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'tema' => 'Fortalecimiento de la comunicación provincial', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'tema' => 'Fortalecimiento del diseño de políticas públicas', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'tema' => 'Medición estadística interna y análisis de datos', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'tema' => 'Modernización Normativa', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'tema' => 'Regiones concertadas', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'tema' => 'Territorialización de los ODS', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9, 'tema' => 'Transformación Digital e Innovación en el Estado', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 10, 'tema' => 'Transversalización de la perspectiva de género en las políticas públicas', 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('temas')->insert($temas);

        // 7. Asignaciones Presupuestarias
        $asignaciones = [
            ['id' => 1, 'asignacion' => 'PAT', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'asignacion' => 'Convenio 2018', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'asignacion' => 'Convenio 2019', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'asignacion' => 'Convenio 2020', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'asignacion' => 'Convenio 2021', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'asignacion' => 'Convenio 2024', 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('asignaciones')->insert($asignaciones);

        // 8. Informes Maestros
        $informes = [
            ['id' => 1, 'informe' => 'Informe de Avance', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'informe' => 'Informe Parcial', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'informe' => 'Informe Final', 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('informes')->insert($informes);

        // 9. Estados de Contratos
        $estados = [
            ['id' => 1, 'estado' => 'Borrador / Sin Ingresar al Área', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'estado' => 'Ingresado al CFI', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'estado' => 'En análisis', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'estado' => 'En trámite', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'estado' => 'En ejecución', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'estado' => 'Finalizado', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'estado' => 'Archivado', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'estado' => 'Recisión', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9, 'estado' => 'Dado de baja', 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('estados')->insert($estados);

        // 10. Crear usuarios
        $usuarios = [
            ['id' => 1, 'usuario' => 'aboix', 'nombre' => 'Andrés', 'apellido' => 'Boix', 'email' => 'aboix@cfi.org.ar','password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'direccion_id' => 2, 'area_id' => 1],
            ['id' => 2, 'usuario' => 'ameyer', 'nombre' => 'Alejandra', 'apellido' => 'Meyer', 'email' => 'ameyer@cfi.org.ar','password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'direccion_id' => 2, 'area_id' => 1],
            ['id' => 3, 'usuario' => 'arojas', 'nombre' => 'Analía', 'apellido' => 'Rojas', 'email' => 'arojas@cfi.org.ar','password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'direccion_id' => 2, 'area_id' => 1],
            ['id' => 4, 'usuario' => 'efrigeni', 'nombre' => 'Ezequiel', 'apellido' => 'Frigeni', 'email' => 'efrigeni@cfi.org.ar','password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'direccion_id' => 2, 'area_id' => 1],
            ['id' => 5, 'usuario' => 'fpascual', 'nombre' => 'Florencia', 'apellido' => 'Pascual', 'email' => 'fpascual@cfi.org.ar','password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'direccion_id' => 2, 'area_id' => 1],
            ['id' => 6, 'usuario' => 'gbmiller', 'nombre' => 'Guillermo', 'apellido' => 'Miller', 'email' => 'gbmiller@cfi.org.ar','password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'direccion_id' => 2, 'area_id' => 1],
            ['id' => 7, 'usuario' => 'jbragagnolo', 'nombre' => 'Jorgelina', 'apellido' => 'Bragagnolo', 'email' => 'jbragagnolo@cfi.org.ar','password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'direccion_id' => 2, 'area_id' => 1],
            ['id' => 8, 'usuario' => 'kfernandez', 'nombre' => 'Karina', 'apellido' => 'Fernández', 'email' => 'kfernandez@cfi.org.ar','password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'direccion_id' => 2, 'area_id' => 1],
            ['id' => 9, 'usuario' => 'ldemaria', 'nombre' => 'Leandro', 'apellido' => 'Demaria', 'email' => 'ldemaria@cfi.org.ar','password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'direccion_id' => 2, 'area_id' => 1],
            ['id' => 10, 'usuario' => 'mbagattin', 'nombre' => 'Marisol', 'apellido' => 'Bagattin', 'email' => 'mbagattin@cfi.org.ar','password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'direccion_id' => 2, 'area_id' => 1],
            ['id' => 11, 'usuario' => 'mcantarelli', 'nombre' => 'Mariana', 'apellido' => 'Cantarelli', 'email' => 'mcantarelli@cfi.org.ar','password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'direccion_id' => 2, 'area_id' => 1],
            ['id' => 12, 'usuario' => 'mdelvalle', 'nombre' => 'Mariana', 'apellido' => 'Del Valle', 'email' => 'mdelvalle@cfi.org.ar','password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'direccion_id' => 2, 'area_id' => 1],
            ['id' => 13, 'usuario' => 'mlenna', 'nombre' => 'María Concepción', 'apellido' => 'Lenna', 'email' => 'mlenna@cfi.org.ar','password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'direccion_id' => 2, 'area_id' => 1],
            ['id' => 14, 'usuario' => 'mlofiego', 'nombre' => 'Marcelo', 'apellido' => 'Lofiego', 'email' => 'mlofiego@cfi.org.ar','password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'direccion_id' => 2, 'area_id' => 1],
            ['id' => 15, 'usuario' => 'moggero', 'nombre' => 'Marcela', 'apellido' => 'Oggero', 'email' => 'moggero@cfi.org.ar','password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'direccion_id' => 2, 'area_id' => 1],
            ['id' => 16, 'usuario' => 'msalvatierra', 'nombre' => 'Marcela', 'apellido' => 'Salvatierra', 'email' => 'msalvatierra@cfi.org.ar','password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'direccion_id' => 2, 'area_id' => 1],
            ['id' => 17, 'usuario' => 'pcarlet', 'nombre' => 'Patricio', 'apellido' => 'Carlet', 'email' => 'pcarlet@cfi.org.ar','password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'direccion_id' => 2, 'area_id' => 1],
            ['id' => 18, 'usuario' => 'rfiguerola', 'nombre' => 'Romina', 'apellido' => 'Figuerola', 'email' => 'rfiguerola@cfi.org.ar','password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'direccion_id' => 2, 'area_id' => 1],
            ['id' => 19, 'usuario' => 'sespindola', 'nombre' => 'Silvia', 'apellido' => 'Espíndola', 'email' => 'sespindola@cfi.org.ar','password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'direccion_id' => 2, 'area_id' => 1],
            ['id' => 20, 'usuario' => 'vcueto', 'nombre' => 'Verónica', 'apellido' => 'Cueto', 'email' => 'vcueto@cfi.org.ar','password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'direccion_id' => 2, 'area_id' => 1],
            ['id' => 21, 'usuario' => 'vgrgic', 'nombre' => 'Victoria', 'apellido' => 'Grgic', 'email' => 'vgrgic@cfi.org.ar','password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'direccion_id' => 2, 'area_id' => 1]
        ];
        DB::table('users')->insert($usuarios);
    }
}
