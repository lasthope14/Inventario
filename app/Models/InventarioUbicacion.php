<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventarioUbicacion extends Model
{
    use HasFactory;

    protected $table = 'inventario_ubicaciones';
    protected $fillable = ['inventario_id', 'ubicacion_id', 'cantidad', 'estado'];

    public function inventario()
    {
        return $this->belongsTo(Inventario::class);
    }

    public function ubicacion()
    {
        return $this->belongsTo(Ubicacion::class);
    }
}