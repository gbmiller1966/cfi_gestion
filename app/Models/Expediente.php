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
            // 1. CÁLCULO DE MONTO TOTAL
            // Usamos floatval para asegurar que sean números y no rompa la suma
            $expediente->monto_imputado = floatval($expediente->monto_convenido) + floatval($expediente->monto_cfi);

            // 2. AUTO-COMPLETAR LA REGIÓN (Solo si cambió la provincia y no tenemos la región)
            if ($expediente->isDirty('provincia_id') && $expediente->provincia_id) {
                // Usamos query builder directo para evitar cargar el modelo completo y disparar eventos
                $regionId = \DB::table('provincias')
                    ->where('id', $expediente->provincia_id)
                    ->value('region_id');
                    
                if ($regionId) {
                    $expediente->region_id = $regionId;
                }
            }

            // 3. CÁLCULO DEL ESTADO (Cascada)
            // Si hay una excepción manual, manda esa.
            if (!empty($expediente->estado_excepcion)) {
                $expediente->estado_id = $expediente->estado_excepcion;
            } else {
                // Determinamos el estado según las fechas
                if ($expediente->f_envio_archivo) {
                    $expediente->estado_id = 7; // Archivado
                } elseif ($expediente->f_aprobacion_sec_gen) {
                    $expediente->estado_id = 6; // Finalizado
                } elseif ($expediente->f_inicio_contrato) {
                    $expediente->estado_id = 5; // En ejecución
                } elseif ($expediente->f_firma_director_tdr) {
                    $expediente->estado_id = 4; // En trámite
                } elseif ($expediente->f_ingreso_area) {
                    $expediente->estado_id = 3; // En análisis
                } elseif ($expediente->f_ingreso_cfi) {
                    $expediente->estado_id = 2; // Ingresado al CFI
                } else {
                    $expediente->estado_id = 1; // Borrador
                }
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
    public function colaboradores(): BelongsToMany { return $this->belongsToMany(User::class, 'expediente_colaboradores'); }
    public function expediente_informes(): HasMany { return $this->hasMany(ExpedienteInforme::class); }
    public function hitos(): HasMany { return $this->hasMany(ExpedienteHito::class); }
    public function informes(): HasMany {return $this->expediente_informes();}
}
