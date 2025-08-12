<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Inventario;

class UpdateInventoryTypes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:update-types';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza algunos equipos a tipo alquiler para pruebas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Verificar distribución actual
        $propio = Inventario::where('tipo_propiedad', 'propio')->count();
        $alquiler = Inventario::where('tipo_propiedad', 'alquiler')->count();
        
        $this->info("Estado actual:");
        $this->info("Equipos propios: {$propio}");
        $this->info("Equipos en alquiler: {$alquiler}");
        
        // Actualizar algunos equipos a alquiler (los últimos 3)
        $equipos = Inventario::orderBy('id', 'desc')->take(3)->get();
        
        foreach ($equipos as $equipo) {
            $equipo->tipo_propiedad = 'alquiler';
            $equipo->save();
            $this->info("Equipo {$equipo->nombre} actualizado a alquiler");
        }
        
        // Verificar nueva distribución
        $propio = Inventario::where('tipo_propiedad', 'propio')->count();
        $alquiler = Inventario::where('tipo_propiedad', 'alquiler')->count();
        
        $this->info("\nNuevo estado:");
        $this->info("Equipos propios: {$propio}");
        $this->info("Equipos en alquiler: {$alquiler}");
        
        return 0;
    }
}
