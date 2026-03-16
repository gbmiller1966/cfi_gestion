<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpedienteHito extends Model
{
    protected $table = 'expediente_hitos';
    protected $guarded = ['id', 'fecha', 'descripcion'];
}
