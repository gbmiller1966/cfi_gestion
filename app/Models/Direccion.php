<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Direccion extends Model
{
    use HasFactory;

    // 1. Forzamos el nombre exacto de la tabla en español
    protected $table = 'direcciones';

    // 2. Permitimos que Filament guarde datos en todas las columnas (excepto el id)
    protected $guarded = ['id'];
}
