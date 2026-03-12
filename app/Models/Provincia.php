<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provincia extends Model
{
    use HasFactory;

    protected $table = 'provincias';
    protected $guarded = ['id'];

    // Una provincia pertenece a una región
    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    // Una provincia tiene muchas localidades
    public function localidades()
    {
        return $this->hasMany(Localidad::class);
    }
}
