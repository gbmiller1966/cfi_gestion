<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expediente extends Model
{
    use HasFactory, SoftDeletes; // Activa el borrado lógico [cite: 90]

    protected $guarded = ['id']; // Permite asignación masiva excepto en el ID

    // Casteo de fechas para que Laravel las trate como objetos Carbon automáticamente [cite: 63, 64]
    protected $casts = [
        'f_ingreso_cfi' => 'datetime',
        'f_ingreso_area' => 'datetime',
        'f_derivacion_tecnico' => 'datetime',
        'f_elevacion_tdr' => 'datetime',
        'f_firma_jefe_tdr' => 'datetime',
        'f_firma_director_tdr' => 'datetime',
        'f_derivacion_compras' => 'datetime',
        'f_inicio_contrato' => 'date',
        'f_fin_contrato' => 'date',
        'f_ingreso_informe_final' => 'datetime',
        'f_aprobacion_contraparte' => 'datetime',
        'f_aprobacion_jefe_tecnico' => 'datetime',
        'f_aprobacion_director' => 'datetime',
        'f_pase_gestion' => 'datetime',
        'f_aprobacion_sec_gen' => 'datetime',
        'f_envio_biblioteca' => 'datetime',
        'f_envio_archivo' => 'datetime',
    ];

    // ==========================================
    // RELACIONES DE PERTENENCIA (BelongsTo)
    // ==========================================

    public function tecnico()
    {
        return $this->belongsTo(User::class, 'user_id'); // Dueño del expediente [cite: 54]
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function provincia()
    {
        return $this->belongsTo(Provincia::class);
    }

    public function localidad()
    {
        return $this->belongsTo(Localidad::class);
    }

    public function contraparte()
    {
        return $this->belongsTo(ContraparteProvincial::class, 'contraparte_id');
    }

    public function asignacionPresupuestaria()
    {
        return $this->belongsTo(AsignacionPresupuestaria::class, 'asignacion_id');
    }

    public function temaEstrategico()
    {
        return $this->belongsTo(TemaEstrategico::class, 'tema_id');
    }

    public function tipoContrato()
    {
        return $this->belongsTo(TipoContrato::class, 'tipo_contrato_id');
    }

    // ==========================================
    // RELACIONES MUCHOS A MUCHOS (BelongsToMany)
    // ==========================================

    public function proveedores()
    {
        return $this->belongsToMany(Proveedor::class, 'expediente_proveedor'); //
    }

    public function colaboradores()
    {
        return $this->belongsToMany(User::class, 'expediente_colaboradores'); // Técnicos adicionales
    }

    public function informes()
    {
        // Traemos también los campos extra de la tabla pivot [cite: 110, 111]
        return $this->belongsToMany(InformeMaestro::class, 'expediente_informes', 'expediente_id', 'informe_id')
                    ->withPivot('estado_entrega', 'fecha_limite')
                    ->withTimestamps();
    }

    // ==========================================
    // RELACIONES HAS MANY (Uno a Muchos)
    // ==========================================

    public function hitos()
    {
        return $this->hasMany(Hito::class); // Log de novedades
    }
}
