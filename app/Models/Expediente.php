<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Expediente extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'f_ingreso_cfi' => 'date',
        'f_ingreso_area' => 'date',
        'f_derivacion_tecnico' => 'date',
        'f_elevacion_tdr' => 'date',
        'f_firma_jefe_tdr' => 'date',
        'f_firma_director_tdr' => 'date',
        'f_derivacion_compras' => 'date',
        'f_inicio_contrato' => 'date',
        'f_fin_contrato' => 'date',
        'f_ingreso_informe_final' => 'date',
        'f_aprobacion_contraparte' => 'date',
        'f_aprobacion_jefe_tecnico' => 'date',
        'f_aprobacion_director' => 'date',
        'f_pase_gestion' => 'date',
        'f_aprobacion_sec_gen' => 'date',
        'f_envio_biblioteca' => 'date',
        'f_envio_archivo' => 'date',
    ];

    protected static function booted()
    {

        static::saving(function ($expediente) {

            $expediente->monto_imputado = ($expediente->monto_convenido ?? 0) + ($expediente->monto_cfi ?? 0);

        // 1. AUTO-COMPLETAR LA REGIÓN
            // Si hay una provincia seleccionada, buscamos a qué región pertenece
            if ($expediente->provincia_id) {
                // Buscamos la provincia en la base de datos
                $provincia = \App\Models\Provincia::find($expediente->provincia_id);

                // Si la encontramos, le copiamos su region_id al expediente
                if ($provincia) {
                    $expediente->region_id = $provincia->region_id;
                }
            }
            // 2. CÁLCULO DEL ESTADO (Cascada de fin a inicio apuntando al ID)

            if (!empty($expediente->estado_excepcion)) {
                // Ojo acá: si 'estado_excepcion' antes guardaba texto,
                // vas a tener que asegurarte de que en el formulario ahora guarde el ID de la excepción.
                $expediente->estado_contrato_id = $expediente->estado_excepcion;
                return;
            }

            if (!empty($expediente->f_envio_archivo)) {
                $expediente->estado_contrato_id = 7; // Reemplazar por el ID real de 'Archivado'

            } elseif (!empty($expediente->f_aprobacion_sec_gen)) {
                $expediente->estado_contrato_id = 6; // Reemplazar por el ID real de 'Finalizado'

            } elseif (!empty($expediente->f_inicio_contrato)) {
                $expediente->estado_contrato_id = 5; // Reemplazar por el ID real de 'En ejecución'

            } elseif (!empty($expediente->f_firma_director_tdr)) {
                $expediente->estado_contrato_id = 4; // Reemplazar por el ID real de 'En trámite'

            } elseif (!empty($expediente->f_ingreso_area)) {
                $expediente->estado_contrato_id = 3; // Reemplazar por el ID real de 'En análisis'

            } elseif (!empty($expediente->f_ingreso_cfi)) {
                $expediente->estado_contrato_id = 2; // Reemplazar por el ID real de 'Ingresado al CFI'

            } else {
                $expediente->estado_contrato_id = 1; // Reemplazar por el ID real de 'Borrador / Sin Ingresar'
            }
        // 2. CÁLCULO DEL ESTADO (Cascada de fin a inicio)
/*             if (!empty($expediente->estado_excepcion)) {
                $expediente->estado = $expediente->estado_excepcion;
                return;
            }

            if (!empty($expediente->f_envio_archivo)) {
                $expediente->estado = 'Archivado';

            } elseif (!empty($expediente->f_aprobacion_sec_gen)) {
                $expediente->estado = 'Finalizado';

            } elseif (!empty($expediente->f_inicio_contrato)) {
                $expediente->estado = 'En ejecución';

            } elseif (!empty($expediente->f_firma_director_tdr)) {
                $expediente->estado = 'En trámite';

            } elseif (!empty($expediente->f_ingreso_area)) {
                $expediente->estado = 'En análisis';

            } elseif (!empty($expediente->f_ingreso_cfi)) {
                $expediente->estado = 'Ingresado al CFI';

            } else {
                $expediente->estado = 'Borrador / Sin Ingresar';
            } */
        });
    }

    // Propiedad y Estructura
    public function tecnico(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
    public function direccion(): BelongsTo { return $this->belongsTo(Direccion::class); }
    public function area(): BelongsTo { return $this->belongsTo(Area::class); }

    // Tablas Maestras
    public function region(): BelongsTo { return $this->belongsTo(Region::class); }
    public function provincia(): BelongsTo { return $this->belongsTo(Provincia::class, 'provincia_id'); }
    public function localidad(): BelongsTo { return $this->belongsTo(Localidad::class); }
    public function contraparte(): BelongsTo { return $this->belongsTo(ContraparteProvincial::class, 'contraparte_id'); }
    public function asignacion(): BelongsTo { return $this->belongsTo(AsignacionPresupuestaria::class, 'asignacion_id'); }
    public function tema(): BelongsTo { return $this->belongsTo(TemaEstrategico::class, 'tema_estrategico_id'); }
    public function tipoContrato(): BelongsTo { return $this->belongsTo(TipoContrato::class, 'tipo_contrato_id'); }
    public function estadoContrato(): BelongsTo { return $this->belongsTo(EstadoContrato::class, 'estado_contrato_id'); }

    // Pivots y Relaciones Hijas
    public function proveedores(): BelongsToMany { return $this->belongsToMany(Proveedor::class, 'expediente_proveedor', 'expediente_id', 'proveedor_id'); }
    public function colaboradores(): BelongsToMany { return $this->belongsToMany(User::class, 'expediente_user', 'expediente_id', 'user_id'); }
    public function informes(): HasMany { return $this->hasMany(ExpedienteInforme::class, 'expediente_id'); }
    public function hitos(): HasMany { return $this->hasMany(ExpedienteHito::class, 'expediente_id'); }
}
