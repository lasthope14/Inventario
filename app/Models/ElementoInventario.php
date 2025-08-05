<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\AsCollection;

class ElementoInventario extends Model
{
    protected $fillable = ['inventario_id', 'identificador', 'fecha_adquisicion', 'estado', 'observaciones'];

    public function inventario()
    {
        return $this->belongsTo(Inventario::class);
    }
}