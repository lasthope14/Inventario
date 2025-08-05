<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Proveedor;
use Illuminate\Support\Facades\Log;

class Mantenimiento extends Model
{
    protected $fillable = [
        'inventario_id', 'tipo', 'fecha_programada', 'fecha_realizado',
        'descripcion', 'resultado', 'responsable_id', 'periodicidad', 'costo',
        'autorizado_por', 'user_id', 'veces_pospuesto'
    ];
    
    protected $nullable = ['responsable_id'];
    protected $dates = ['fecha_programada', 'fecha_realizado', 'created_at', 'updated_at'];
    
    protected static function booted()
    {
        static::creating(function ($mantenimiento) {
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10);
            $caller = collect($trace)->map(function ($trace) {
                return [
                    'file' => $trace['file'] ?? null,
                    'line' => $trace['line'] ?? null,
                    'function' => $trace['function'] ?? null,
                    'class' => $trace['class'] ?? null,
                ];
            })->take(5);

            Log::info('Iniciando creación de modelo Mantenimiento', [
                'data' => $mantenimiento->toArray(),
                'trace' => $caller->toArray(),
                'session_id' => session()->getId()
            ]);
        });

        static::created(function ($mantenimiento) {
            Log::info('Modelo Mantenimiento creado', [
                'id' => $mantenimiento->id,
                'tipo' => $mantenimiento->tipo,
                'fecha_programada' => $mantenimiento->fecha_programada,
                'session_id' => session()->getId()
            ]);
        });
    }
    
    public function getFechaProgramadaAttribute($value)
    {
        return $value ? Carbon::parse($value) : null;
    }

    public function getFechaRealizadoAttribute($value)
    {
        return $value ? Carbon::parse($value) : null;
    }

    public function inventario()
    {
        return $this->belongsTo(Inventario::class);
    }

    public function responsable()
    {
        return $this->belongsTo(Proveedor::class, 'responsable_id');
    }

    public function solicitadoPor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getVecesPospuestoAttribute($value)
    {
        return $value ?? 0;
    }
}