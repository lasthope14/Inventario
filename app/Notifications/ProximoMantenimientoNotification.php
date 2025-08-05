<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Mantenimiento;
use Illuminate\Support\Facades\Log;

class ProximoMantenimientoNotification extends Notification
{
    use Queueable;

    protected $mantenimiento;

    public function __construct(Mantenimiento $mantenimiento)
    {
        $this->mantenimiento = $mantenimiento;
    }

    public function via($notifiable)
{
    if (!$notifiable->shouldReceiveNotification()) {
        return [];
    }
    return ['database', 'mail']; // Simplificar y siempre incluir mail
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
        Log::info('Preparando notificación para la base de datos', ['user_id' => $notifiable->id]);
        $data = [
            'mantenimiento_id' => $this->mantenimiento->id,
            'inventario_nombre' => $this->mantenimiento->inventario->nombre,
            'fecha_programada' => $this->mantenimiento->fecha_programada->format('d/m/Y'),
            'tipo' => $this->mantenimiento->tipo, // Añade esta línea
        ];
        Log::info('Datos de notificación preparados', $data);
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