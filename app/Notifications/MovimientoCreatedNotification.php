<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Movimiento;
use Illuminate\Support\Facades\Log;

class MovimientoCreatedNotification extends Notification
{
    use Queueable;

    protected $movimiento;

    public function __construct(Movimiento $movimiento)
    {
        $this->movimiento = $movimiento;
        Log::info('Creando nueva instancia de MovimientoCreatedNotification', [
            'movimiento_id' => $movimiento->id,
            'session_id' => session()->getId()
        ]);
    }

    public function via($notifiable)
    {
        Log::info('Determinando canales de notificación', [
            'notifiable_id' => $notifiable->id,
            'movimiento_id' => $this->movimiento->id,
            'session_id' => session()->getId()
        ]);

        if (!$notifiable->shouldReceiveNotification()) {
            Log::info('Usuario no debe recibir notificaciones', [
                'user_id' => $notifiable->id,
                'session_id' => session()->getId()
            ]);
            return [];
        }

        return ['database', 'mail'];
    }

    public function toMail($notifiable)
{
    return (new MailMessage)
        ->subject('Nuevo Movimiento Registrado')
        ->view('emails.movimiento-created', [
            'movimiento' => $this->movimiento,
            'usuario' => $notifiable,
            'tipo' => 'nuevo'
        ]);
}

    public function toDatabase($notifiable)
    {
        Log::info('Preparando notificación para base de datos', [
            'notifiable_id' => $notifiable->id,
            'movimiento_id' => $this->movimiento->id,
            'session_id' => session()->getId()
        ]);

        $ubicacionOrigen = \App\Models\Ubicacion::find($this->movimiento->ubicacion_origen);
        $ubicacionDestino = \App\Models\Ubicacion::find($this->movimiento->ubicacion_destino);
        
        $data = [
            'tipo' => 'movimiento_created',
            'movimiento_id' => $this->movimiento->id,
            'inventario_nombre' => $this->movimiento->inventario->nombre,
            'ubicacion_origen' => $ubicacionOrigen ? $ubicacionOrigen->nombre : $this->movimiento->ubicacion_origen,
            'ubicacion_destino' => $ubicacionDestino ? $ubicacionDestino->nombre : $this->movimiento->ubicacion_destino,
            'fecha_movimiento' => $this->movimiento->fecha_movimiento->format('d/m/Y H:i'),
            'cantidad' => $this->movimiento->cantidad
        ];

        Log::info('Datos de notificación preparados', [
            'data' => $data,
            'session_id' => session()->getId()
        ]);
        
        return $data;
    }

    public function toArray($notifiable)
    {
        $ubicacionOrigen = \App\Models\Ubicacion::find($this->movimiento->ubicacion_origen);
        $ubicacionDestino = \App\Models\Ubicacion::find($this->movimiento->ubicacion_destino);
        
        return [
            'tipo' => 'movimiento_created',
            'movimiento_id' => $this->movimiento->id,
            'inventario_nombre' => $this->movimiento->inventario->nombre,
            'ubicacion_origen' => $ubicacionOrigen ? $ubicacionOrigen->nombre : $this->movimiento->ubicacion_origen,
            'ubicacion_destino' => $ubicacionDestino ? $ubicacionDestino->nombre : $this->movimiento->ubicacion_destino,
            'fecha_movimiento' => $this->movimiento->fecha_movimiento->format('d/m/Y H:i'),
            'cantidad' => $this->movimiento->cantidad
        ];
    }
}