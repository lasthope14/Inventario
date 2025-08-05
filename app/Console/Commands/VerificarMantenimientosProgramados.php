<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Mantenimiento;
use App\Models\User;
use App\Models\Role;
use App\Notifications\ProximoMantenimientoNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class VerificarMantenimientosProgramados extends Command
{
    protected $signature = 'mantenimientos:verificar';
    protected $description = 'Verifica mantenimientos próximos y notifica a los administradores';

    public function handle()
    {
        $mantenimientos = Mantenimiento::where('fecha_programada', '>', Carbon::now())
            ->where('fecha_programada', '<=', Carbon::now()->addDays(7))
            ->where('fecha_realizado', null)
            ->get();

        Log::info('Verificando mantenimientos programados', ['count' => $mantenimientos->count()]);

        $rolAdministrador = Role::where('name', 'administrador')->first();

        if (!$rolAdministrador) {
            Log::error('No se encontró el rol de administrador');
            return;
        }

        $administradores = User::where('role_id', $rolAdministrador->id)->get();

        Log::info('Administradores encontrados', ['count' => $administradores->count()]);

        foreach ($mantenimientos as $mantenimiento) {
            foreach ($administradores as $admin) {
                try {
                    $admin->notify(new ProximoMantenimientoNotification($mantenimiento));
                    Log::info('Notificación enviada', [
                        'user_id' => $admin->id, 
                        'mantenimiento_id' => $mantenimiento->id
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error al enviar notificación', [
                        'user_id' => $admin->id, 
                        'mantenimiento_id' => $mantenimiento->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        $this->info('Notificaciones de mantenimiento enviadas con éxito.');
        Log::info('Proceso de verificación de mantenimientos completado');
    }
}