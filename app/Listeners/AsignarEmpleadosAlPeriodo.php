<?php

namespace App\Listeners;

use App\Events\PeriodoCreado;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Empleado;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Events\Dispatchable;

class AsignarEmpleadosAlPeriodo 
{
    use Dispatchable, InteractsWithQueue;

    public function handle(PeriodoCreado $event)
    {
        if (!$event->periodo->tipo || !$event->periodo->tipo->frecuencia) {
            return;
        }
    
        $empleados = Empleado::with(['prestamos' => function($query) {
            $query->where('activo', true)
                  ->where('cuota_actual', '<', DB::raw('cuotas'));
        }])
        ->where('id_tnomina', $event->periodo->id_tnomina)
        ->get();
    
        foreach ($empleados as $empleado) {
            $factor = $this->calcularFactor($event->periodo->tipo->frecuencia);
            $totalPagado = $empleado->salario_base * $factor;
    
            $totalPrestamos = 0;
            $totalDescuentos = $empleado->descuentos()
                ->where('d_fecha', '>=', $event->periodo->inicio)
                ->where('d_fecha', '<=', $event->periodo->fin)
                ->sum('valor');
                
            $totalAbonos = $empleado->abonos()
                ->where('a_fecha', '>=', $event->periodo->inicio)
                ->where('a_fecha', '<=', $event->periodo->fin)
                ->sum('valor');

            $totalCostos = $empleado->costos()
                ->where('f_costos', '>=', $event->periodo->inicio)
                ->where('f_costos', '<=', $event->periodo->fin)
                ->sum('valor');    
                
    
                foreach ($empleado->prestamos as $prestamo) {
                    try {
                        if ($prestamo->cuota_actual >= $prestamo->cuotas) {
                            $prestamo->cuota_actual = 0;
                            $prestamo->save();
                        }
                
                        $cuotaPendiente = $prestamo->cuotas()
                            ->where('pagada', false)
                            ->orderBy('created_at')
                            ->first();
                
                        if ($cuotaPendiente) {
                            DB::transaction(function () use ($cuotaPendiente, $prestamo, &$totalPrestamos) {
                                $cuotaPendiente->update([
                                    'pagada' => true,
                                    'updated_at' => now()
                                ]);

                                $prestamo->increment('cuota_actual');

                                $totalPrestamos += $cuotaPendiente->valor;

                                $cuotasRestantes = $prestamo->cuotas()
                                    ->where('pagada', false)
                                    ->count();
                                    
                                if ($cuotasRestantes === 0) {
                                    $prestamo->update([
                                        'activo' => 0, 
                                        'cuota_actual' => $prestamo->cuotas
                                    ]);
                                }
                            });
                        }
                        
                    } catch (\Exception $e) {
                        logger()->error("Error procesando préstamo {$prestamo->id}", [
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                        continue;
                    }
                }
    
            $event->periodo->empleados()->attach($empleado->id_empleado, [
                'total_descuentos' => $totalDescuentos,
                'total_abonos' => $totalAbonos,
                'total_prestamos' => $totalPrestamos,
                'total_costos' => $totalCostos,
                'total_pagado' => $totalPagado - $totalPrestamos - $totalDescuentos - $totalCostos + $totalAbonos
            ]);
        }
    }

    public function calcularFactor($frecuencia)
    {
        return match ($frecuencia) {
            1 => 0.5,
            2 => 1,
            3 => 0.25,
            default => 0
        };
    }
}