<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpedienteHito extends Model
{
    protected $table = 'expediente_hitos';
    protected $guarded = ['id'];
    protected $casts = ['fecha' => 'date', // 👈 ESTO ES LA CLAVE: Convierte el string en un objeto Carbon
    ];
}
