<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Localidad extends Model
{
    use HasFactory;

    protected $table = 'localidades';
    protected $guarded = ['id'];

    // Una localidad pertenece a una provincia
    public function provincia()
    {
        return $this->belongsTo(Provincia::class);
    }
}
