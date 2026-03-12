<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'proveedores';
    protected $guarded = ['id'];

    public function provincia()
    {
        return $this->belongsTo(Provincia::class);
    }

    // Relación: Un proveedor puede estar en muchos expedientes
    public function expedientes()
    {
        return $this->belongsToMany(Expediente::class, 'expediente_proveedor');
    }
}
