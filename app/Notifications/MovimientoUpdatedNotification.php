<?php
namespace App\Notifications;

use App\Models\Movimiento;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class MovimientoUpdatedNotification extends Notification
{
    use Queueable;

    protected $movimiento;
    protected $cambios;

    public function __construct(Movimiento $movimiento, array $cambios)
    {
        $this->movimiento = $movimiento;
        $this->cambios = $cambios;
        Log::info('Creando nueva instancia de MovimientoUpdatedNotification', [
            'movimiento_id' => $movimiento->id,
            'cambios' => $cambios,
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
        ->subject('Movimiento Actualizado')
        ->view('emails.movimiento-updated', [
            'movimiento' => $this->movimiento,
            'cambios' => $this->cambios,
            'usuario' => $notifiable
        ]);
}

    public function toDatabase($notifiable)
    {
        Log::info('Preparando notificación para base de datos', [
            'notifiable_id' => $notifiable->id,
            'movimiento_id' => $this->movimiento->id,
            'session_id' => session()->getId()
        ]);

        $data = [
            'tipo' => 'movimiento_updated',
            'movimiento_id' => $this->movimiento->id,
            'inventario_nombre' => $this->movimiento->inventario->nombre,
            'cambios' => $this->cambios,
            'ubicacion_origen' => \App\Models\Ubicacion::find($this->movimiento->ubicacion_origen)?->nombre ?? $this->movimiento->ubicacion_origen,
            'ubicacion_destino' => \App\Models\Ubicacion::find($this->movimiento->ubicacion_destino)?->nombre ?? $this->movimiento->ubicacion_destino,
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
        return [
            'tipo' => 'movimiento_updated',
            'movimiento_id' => $this->movimiento->id,
            'inventario_nombre' => $this->movimiento->inventario->nombre,
            'cambios' => $this->cambios,
            'fecha_movimiento' => $this->movimiento->fecha_movimiento->format('d/m/Y H:i')
        ];
    }
}