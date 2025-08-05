<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class MovimientoDeletedNotification extends Notification
{
    use Queueable;

    protected $detallesMovimiento;

    public function __construct(array $detallesMovimiento)
    {
        $this->detallesMovimiento = $detallesMovimiento;
        Log::info('Creando nueva instancia de MovimientoDeletedNotification', [
            'detalles' => $detallesMovimiento,
            'session_id' => session()->getId()
        ]);
    }

    public function via($notifiable)
    {
        Log::info('Determinando canales de notificación', [
            'notifiable_id' => $notifiable->id,
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
        ->subject('Movimiento Eliminado')
        ->view('emails.movimiento-deleted', [
            'detalles' => $this->detallesMovimiento,
            'usuario' => $notifiable
        ]);
}

    public function toDatabase($notifiable)
    {
        Log::info('Preparando notificación para base de datos', [
            'notifiable_id' => $notifiable->id,
            'session_id' => session()->getId()
        ]);

        $data = [
            'tipo' => 'movimiento_deleted',
            'inventario_nombre' => $this->detallesMovimiento['inventario_nombre'],
            'ubicacion_origen' => $this->detallesMovimiento['ubicacion_origen'],
            'ubicacion_destino' => $this->detallesMovimiento['ubicacion_destino'],
            'fecha_movimiento' => $this->detallesMovimiento['fecha_movimiento'],
            'cantidad' => $this->detallesMovimiento['cantidad']
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
            'tipo' => 'movimiento_deleted',
            'inventario_nombre' => $this->detallesMovimiento['inventario_nombre'],
            'ubicacion_origen' => $this->detallesMovimiento['ubicacion_origen'],
            'ubicacion_destino' => $this->detallesMovimiento['ubicacion_destino'],
            'fecha_movimiento' => $this->detallesMovimiento['fecha_movimiento'],
            'cantidad' => $this->detallesMovimiento['cantidad']
        ];
    }
}