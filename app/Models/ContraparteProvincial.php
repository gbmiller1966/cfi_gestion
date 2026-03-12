<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContraparteProvincial extends Model
{
    use HasFactory;

    protected $table = 'contrapartes_provinciales';
    protected $guarded = ['id'];

    // Relación: Una contraparte pertenece a una provincia
    public function provincia()
    {
        return $this->belongsTo(Provincia::class);
    }
}
