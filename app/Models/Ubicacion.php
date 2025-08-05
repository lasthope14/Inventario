<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ubicacion extends Model
{
    protected $table = 'ubicaciones';
    protected $fillable = ['nombre', 'descripcion'];

    public function inventarios()
    {
        return $this->hasMany(Inventario::class);
    }
}