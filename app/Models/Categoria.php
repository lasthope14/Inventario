<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $fillable = ['nombre', 'prefijo', 'imagen'];

    public function inventarios()
    {
        return $this->hasMany(Inventario::class);
    }
}