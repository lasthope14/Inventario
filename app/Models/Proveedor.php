<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'proveedores';

    protected $fillable = ['nombre', 'contacto', 'telefono', 'email'];

    public function inventarios()
    {
        return $this->hasMany(Inventario::class);
    }
}