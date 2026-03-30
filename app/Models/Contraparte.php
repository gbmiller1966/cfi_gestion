<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contraparte extends Model
{
    use HasFactory;

    protected $table = 'contrapartes';
    protected $guarded = ['id'];

    // Relación: Una contraparte pertenece a una provincia
    public function provincia()
    {
        return $this->belongsTo(Provincia::class);
    }
}
