<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Movimiento extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventario_id',
        'ubicacion_origen',
        'ubicacion_destino',
        'usuario_origen_id',
        'usuario_destino_id',
        'cantidad',
        'motivo',
        'nuevo_estado',
        'fecha_movimiento',
        'realizado_por_id',
        'revertido',
        'revertido_at',
        'revertido_por',
        'movimiento_original_id',
        'tipo_movimiento'
    ];

    protected $dates = ['fecha_movimiento', 'created_at', 'updated_at'];

    protected $casts = [
        'revertido' => 'boolean',
        'revertido_at' => 'datetime',
    ];

    public function inventario()
    {
        return $this->belongsTo(Inventario::class)
            ->withDefault([
                'nombre' => 'N/A',
                'codigo_unico' => 'N/A'
            ]);
    }

    public function usuarioOrigen()
    {
        return $this->belongsTo(Empleado::class, 'usuario_origen_id')
            ->withDefault([
                'nombre' => 'N/A'
            ]);
    }

    public function usuarioDestino()
    {
        return $this->belongsTo(Empleado::class, 'usuario_destino_id')
            ->withDefault([
                'nombre' => 'N/A'
            ]);
    }

    public function realizadoPor()
    {
        return $this->belongsTo(User::class, 'realizado_por_id')
            ->withDefault([
                'name' => 'Usuario eliminado'
            ]);
    }

    public function ubicacionOrigen()
    {
        return $this->belongsTo(Ubicacion::class, 'ubicacion_origen')
            ->withDefault([
                'nombre' => 'N/A'
            ]);
    }

    public function ubicacionDestino()
    {
        return $this->belongsTo(Ubicacion::class, 'ubicacion_destino')
            ->withDefault([
                'nombre' => 'N/A'
            ]);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_origen_id');
    }

    public function movimientos()
    {
        return $this->hasMany(Movimiento::class);
    }

    public function getFechaMovimientoAttribute($value)
    {
        return $value ? Carbon::parse($value) : null;
    }

    public function setFechaMovimientoAttribute($value)
    {
        if ($value) {
            $this->attributes['fecha_movimiento'] = Carbon::parse($value)->format('Y-m-d H:i:s');
        }
    }
}