<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Mantenimiento;
use Illuminate\Support\Facades\Log;

class NuevoMantenimientoNotification extends Notification
{
    use Queueable;

    protected $mantenimiento;

    public function __construct(Mantenimiento $mantenimiento)
    {
        $this->mantenimiento = $mantenimiento;
        Log::info('Creando nueva instancia de NuevoMantenimientoNotification', [
            'mantenimiento_id' => $mantenimiento->id,
            'tipo' => $mantenimiento->tipo,
            'session_id' => session()->getId()
        ]);
    }

    public function via($notifiable)
    {
        Log::info('Determinando canales de notificación', [
            'notifiable_id' => $notifiable->id,
            'mantenimiento_id' => $this->mantenimiento->id,
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
        ->view('emails.mantenimiento', [ // Cambiar nombre de la vista
            'tipo' => $this->mantenimiento->tipo,
            'elemento' => $this->mantenimiento->inventario->nombre,
            'fecha' => $this->mantenimiento->fecha_programada->format('d/m/Y'),
            'descripcion' => $this->mantenimiento->descripcion,
            'mantenimiento' => $this->mantenimiento
        ]);
}

    public function toDatabase($notifiable)
    {
        Log::info('Preparando notificación para base de datos', [
            'notifiable_id' => $notifiable->id,
            'mantenimiento_id' => $this->mantenimiento->id,
            'session_id' => session()->getId()
        ]);

        $data = [
            'mantenimiento_id' => $this->mantenimiento->id,
            'inventario_nombre' => $this->mantenimiento->inventario->nombre,
            'fecha_programada' => $this->mantenimiento->fecha_programada->format('d/m/Y'),
            'tipo' => $this->mantenimiento->tipo,
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
            'mantenimiento_id' => $this->mantenimiento->id,
            'inventario_nombre' => $this->mantenimiento->inventario->nombre,
            'fecha_programada' => $this->mantenimiento->fecha_programada->format('d/m/Y'),
            'tipo' => $this->mantenimiento->tipo,
        ];
    }
}