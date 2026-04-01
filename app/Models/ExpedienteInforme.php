<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpedienteInforme extends Model
{
        protected $table = 'expediente_informes';
        protected $guarded = ['id'];
        protected $casts = [
            'fecha_limite' => 'date',
        ];
    public function informe()
    {
        // Asegurate de que InformeMaestro::class sea el nombre real de tu modelo de tipos de informes
        return $this->belongsTo(Informe::class, 'informe_id');
    }
}
