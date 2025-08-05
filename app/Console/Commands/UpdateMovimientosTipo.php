<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Movimiento;
use Illuminate\Support\Facades\DB;

class UpdateMovimientosTipo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'movimientos:update-tipo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza el tipo de movimientos existentes (individual/masivo)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando actualización de tipos de movimientos...');
        
        // Buscar movimientos que fueron creados en lotes (mismo minuto, múltiples movimientos)
        $movimientosMasivos = DB::select("
            SELECT m1.id 
            FROM movimientos m1
            WHERE EXISTS (
                SELECT 1 
                FROM movimientos m2 
                WHERE m2.id != m1.id 
                AND DATE_FORMAT(m1.created_at, '%Y-%m-%d %H:%i') = DATE_FORMAT(m2.created_at, '%Y-%m-%d %H:%i')
            )
            AND (m1.tipo_movimiento IS NULL OR m1.tipo_movimiento = 'individual')
        ");
        
        $idsMovimientosMasivos = collect($movimientosMasivos)->pluck('id')->toArray();
        
        if (count($idsMovimientosMasivos) > 0) {
            // Actualizar movimientos masivos
            $actualizadosMasivos = Movimiento::whereIn('id', $idsMovimientosMasivos)
                ->update(['tipo_movimiento' => 'masivo']);
            
            $this->info("✅ {$actualizadosMasivos} movimientos marcados como 'masivo'");
        } else {
            $this->info("ℹ️  No se encontraron movimientos para marcar como masivos");
        }
        
        // Actualizar el resto como individuales
        $actualizadosIndividuales = Movimiento::where('tipo_movimiento', 'individual')
            ->orWhereNull('tipo_movimiento')
            ->whereNotIn('id', $idsMovimientosMasivos)
            ->update(['tipo_movimiento' => 'individual']);
        
        $this->info("✅ {$actualizadosIndividuales} movimientos marcados como 'individual'");
        
        // Mostrar estadísticas finales
        $totalMasivos = Movimiento::where('tipo_movimiento', 'masivo')->count();
        $totalIndividuales = Movimiento::where('tipo_movimiento', 'individual')->count();
        
        $this->info("\n📊 Estadísticas finales:");
        $this->info("   • Movimientos masivos: {$totalMasivos}");
        $this->info("   • Movimientos individuales: {$totalIndividuales}");
        $this->info("   • Total: " . ($totalMasivos + $totalIndividuales));
        
        $this->info("\n🎉 Actualización completada exitosamente!");
        
        return 0;
    }
}
