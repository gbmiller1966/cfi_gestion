<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TablasMaestrasSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Regiones (No dependen de nadie)
/*         $regiones = [
            ['id' => 1, 'nombre' => 'Centro', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nombre' => 'Patagonia', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'nombre' => 'Norte Grande', 'created_at' => now(), 'updated_at' => now()],
            // Agregá el resto...
        ];
        DB::table('regiones')->insert($regiones);

        // 2. Provincias (Dependen de regiones)
        $provincias = [
            ['id' => 1, 'region_id' => 1, 'provincia' => 'Córdoba', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'region_id' => 1, 'provincia' => 'Santa Fe', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'region_id' => 2, 'provincia' => 'Chubut', 'created_at' => now(), 'updated_at' => now()],
            // Completar las 24...
        ];
        DB::table('provincias')->insert($provincias);

        // 3. Localidades (Dependen de provincias)
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
        $tiposContrato = [
            ['id' => 1, 'tipo_contrato' => 'LO Institución', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'tipo_contrato' => 'LO Experto', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'tipo_contrato' => 'LO Consultora', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'tipo_contrato' => 'LO Fundación', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'tipo_contrato' => 'LO Cooperativa', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'tipo_contrato' => 'LO Servicios', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'tipo_contrato' => 'LO Grupo de Expertos', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'tipo_contrato' => 'LO Grupo de Consultoras', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9, 'tipo_contrato' => 'LO Convenio', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 10, 'tipo_contrato' => 'LO Convenio-Institución', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 11, 'tipo_contrato' => 'LO Convenio-Experto', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 12, 'tipo_contrato' => 'LO Convenio-Consultora', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 13, 'tipo_contrato' => 'LO Convenio-Fundación', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 14, 'tipo_contrato' => 'Pago por factura', 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('tipos_contrato')->insert($tiposContrato);

        // 6. Temas Estratégicos
        $temas = [
            ['id' => 1, 'tema_estrategico' => 'Análisis y relevamiento económico', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'tema_estrategico' => 'Educación', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'tema_estrategico' => 'Fortalecimiento de la comunicación provincial', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'tema_estrategico' => 'Modernización Normativa', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'tema_estrategico' => 'Territorialización de los ODS', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'tema_estrategico' => 'Transformación Digital e Innovación en el Estado', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'tema_estrategico' => 'Transversalización de la perspectiva de género en las políticas públicas', 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('temas_estrategicos')->insert($temas);

        // 7. Asignaciones Presupuestarias
        $asignaciones = [
            ['id' => 1, 'asignacion_presupuestaria' => 'PAT', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'asignacion_presupuestaria' => 'Convenio 2018', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'asignacion_presupuestaria' => 'Convenio 2019', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'asignacion_presupuestaria' => 'Convenio 2020', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'asignacion_presupuestaria' => 'Convenio 2021', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'asignacion_presupuestaria' => 'Convenio 2024', 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('asignaciones_presupuestarias')->insert($asignaciones);

        // 8. Informes Maestros
        $informes = [
            ['id' => 1, 'informe_maestro' => 'Informe de Avance', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'informe_maestro' => 'Informe Parcial', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'informe_maestro' => 'Informe Final', 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('informes_maestros')->insert($informes);

        // 9. Estados de Contratos
        $estados = [
            ['id' => 1, 'nombre' => 'Borrador / Sin Ingresar', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nombre' => 'Ingresado al CFI', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'nombre' => 'En análisis', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'nombre' => 'En trámite', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'nombre' => 'En ejecución', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'nombre' => 'Finalizado', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'nombre' => 'Archivado', 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('estados_contrato')->insert($estados);
    }
}