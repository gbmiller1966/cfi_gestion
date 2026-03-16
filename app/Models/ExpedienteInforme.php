<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpedienteInforme extends Model
{
    public function informeMaestro()
    {
        // Asegurate de que InformeMaestro::class sea el nombre real de tu modelo de tipos de informes
        return $this->belongsTo(InformeMaestro::class, 'informe_id');
    }
}
