<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    protected $fillable = ['nombre', 'ruta', 'inventario_id', 'hash'];

    public function inventario()
    {
        return $this->belongsTo(Inventario::class);
    }
}