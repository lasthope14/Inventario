<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $fillable = ['nombre', 'prefijo'];

    public function inventarios()
    {
        return $this->hasMany(Inventario::class);
    }
}