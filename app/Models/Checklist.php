<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Checklist extends Model
{
    protected $fillable = ['nombre', 'ruta', 'inventario_id'];

    public function inventario()
    {
        return $this->belongsTo(Inventario::class);
    }
}