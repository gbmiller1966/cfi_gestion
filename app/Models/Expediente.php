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
            // CÁLCULO DE MONTO TOTAL
            $expediente->monto_imputado = ($expediente->monto_convenido ?? 0) + ($expediente->monto_cfi ?? 0);

            // 1. AUTO-COMPLETAR LA REGIÓN
            if ($expediente->provincia_id) {
                $provincia = \App\Models\Provincia::find($expediente->provincia_id);
                if ($provincia) {
                    $expediente->region_id = $provincia->region_id;
                }
            }

            // 2. CÁLCULO DEL ESTADO (Cascada de fin a inicio)
            if (!empty($expediente->estado_excepcion)) {
                $expediente->estado_id = $expediente->estado_excepcion; // Suponiendo que renombraste la columna estado_contrato_id a estado_id
                return;
            }

            if (!empty($expediente->f_envio_archivo)) {
                $expediente->estado_id = 7; // Archivado
            } elseif (!empty($expediente->f_aprobacion_sec_gen)) {
                $expediente->estado_id = 6; // Finalizado
            } elseif (!empty($expediente->f_inicio_contrato)) {
                $expediente->estado_id = 5; // En ejecución
            } elseif (!empty($expediente->f_firma_director_tdr)) {
                $expediente->estado_id = 4; // En trámite
            } elseif (!empty($expediente->f_ingreso_area)) {
                $expediente->estado_id = 3; // En análisis
            } elseif (!empty($expediente->f_ingreso_cfi)) {
                $expediente->estado_id = 2; // Ingresado al CFI
            } else {
                $expediente->estado_id = 1; // Borrador
            }
        });
    }

    // --- ESTRUCTURA ORGANIZATIVA ---
    public function tecnico(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
    public function direccion(): BelongsTo { return $this->belongsTo(Direccion::class); }
    public function area(): BelongsTo { return $this->belongsTo(Area::class); }

    // --- TABLAS MAESTRAS (NOMBRES CORTOS) ---
    public function region(): BelongsTo { return $this->belongsTo(Region::class); }
    public function provincia(): BelongsTo { return $this->belongsTo(Provincia::class); }
    public function localidad(): BelongsTo { return $this->belongsTo(Localidad::class); }
    
    // Aquí aplicamos los cambios de nombres
    public function contraparte(): BelongsTo { return $this->belongsTo(Contraparte::class); }
    public function asignacion(): BelongsTo { return $this->belongsTo(Asignacion::class); }
    public function tema(): BelongsTo { return $this->belongsTo(Tema::class); }
    public function tipo(): BelongsTo { return $this->belongsTo(Tipo::class); }
    public function estado(): BelongsTo { return $this->belongsTo(Estado::class, 'estado_id'); }

    // --- RELACIONES HIJAS Y PIVOTS ---
    public function proveedores(): BelongsToMany { return $this->belongsToMany(Proveedor::class, 'expediente_proveedor'); }
    public function colaboradores(): BelongsToMany { return $this->belongsToMany(User::class, 'expediente_user'); }
    public function expediente_informes(): HasMany { return $this->hasMany(ExpedienteInforme::class); }
    public function hitos(): HasMany { return $this->hasMany(ExpedienteHito::class); }
    public function informes(): HasMany {return $this->expediente_informes();}
}
