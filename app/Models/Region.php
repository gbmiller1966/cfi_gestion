<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use HasFactory;

    protected $table = 'regiones';
    protected $guarded = ['id'];

    // Una región tiene muchas provincias
    public function provincias()
    {
        return $this->hasMany(Provincia::class);
    }
}
