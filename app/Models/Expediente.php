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

    // Propiedad y Estructura
    public function tecnico(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
    public function direccion(): BelongsTo { return $this->belongsTo(Direccion::class); }
    public function area(): BelongsTo { return $this->belongsTo(Area::class); }

    // Tablas Maestras
    public function region(): BelongsTo { return $this->belongsTo(Region::class); }
    public function provincia(): BelongsTo { return $this->belongsTo(Provincia::class); }
    public function localidad(): BelongsTo { return $this->belongsTo(Localidad::class); }
    public function contraparte(): BelongsTo { return $this->belongsTo(ContraparteProvincial::class, 'contraparte_id'); }
    public function asignacion(): BelongsTo { return $this->belongsTo(AsignacionPresupuestaria::class, 'asignacion_id'); }
    public function tema(): BelongsTo { return $this->belongsTo(TemaEstrategico::class, 'tema_id'); }
    public function tipoContrato(): BelongsTo { return $this->belongsTo(TipoContrato::class, 'tipo_contrato_id'); }
    public function estadoContrato(): BelongsTo { return $this->belongsTo(EstadoContrato::class, 'estado_contrato_id'); }

    // Pivots y Relaciones Hijas
    public function proveedores(): BelongsToMany { return $this->belongsToMany(Proveedor::class, 'expediente_proveedor', 'expediente_id', 'proveedor_id'); }
    public function colaboradores(): BelongsToMany { return $this->belongsToMany(User::class, 'expediente_colaboradores'); }
    public function informes(): HasMany { return $this->hasMany(ExpedienteInforme::class, 'expediente_id'); }
    public function hitos(): HasMany { return $this->hasMany(Hito::class); }
}