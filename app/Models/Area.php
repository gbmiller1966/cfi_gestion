<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Direccion;

class Area extends Model
{
    use HasFactory;

    // 1. Forzamos el nombre en español y permitimos asignación masiva
    protected $table = 'areas';
    protected $guarded = ['id'];

    // 2. Definimos la relación: Un área pertenece a una dirección
    public function direccion()
    {
        return $this->belongsTo(Direccion::class);
    }
}
