<?php

namespace App\Listeners;

use App\Events\PeriodoCreado;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Empleado;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Events\Dispatchable;

class AsignarEmpleadosAlPeriodo 
{
    use Dispatchable, InteractsWithQueue;

    public function handle(PeriodoCreado $event)
    {

        $testEmpleado = Empleado::find(1);
        $testPrestamos = $testEmpleado->prestamos()->where('activo', 1)->get();

        Log::info('DIAGNÓSTICO:');
        Log::info('Empleado ID: '.$testEmpleado->id_empleado);
        Log::info('Préstamos directos: '.$testPrestamos->count());

        foreach ($testPrestamos as $p) {
            Log::info("Préstamo ID: {$p->id_prestamos}, Cuotas: {$p->cuotas}, Actual: {$p->cuota_actual}");
            Log::info("Cuotas pendientes: ".$p->cuotas()->where('pagada',0)->count());
        }

        Log::info('=== INICIANDO PROCESO DE ASIGNACIÓN DE EMPLEADOS AL PERIODO ===');
        Log::info('Periodo ID: ' . $event->periodo->id_pnomina);
        
        // Validar que el periodo tenga tipo y frecuencia
        if (!$event->periodo->tipo || !$event->periodo->tipo->frecuencia) {
            Log::error('Periodo sin tipo válido o frecuencia no definida');
            return;
        }
    
        Log::info('Buscando empleados para tipo de nómina: ' . $event->periodo->id_tnomina);
        $empleados = Empleado::with(['prestamos' => function($query) {
            Log::info('Cargando relación de préstamos para empleados');
            $query->where('activo', true)
                  ->where('cuota_actual', '<', DB::raw('cuotas'));
        }])
        ->where('id_tnomina', $event->periodo->id_tnomina)
        ->get();
    
        Log::info('Empleados encontrados: ' . $empleados->count());
    
        foreach ($empleados as $empleado) {
            Log::info('Procesando empleado: ' . $empleado->id_empleado . ' - ' . $empleado->nombre);
            Log::info('Salario base: ' . $empleado->salario_base);
            
            $factor = $this->calcularFactor($event->periodo->tipo->frecuencia);
            $totalPagado = $empleado->salario_base * $factor;
            Log::info('Total a pagar (sin descuentos): ' . $totalPagado);
    
            $totalPrestamos = 0;
            Log::info('Prestamos activos para este empleado: ' . $empleado->prestamos->count());

            $totalDescuentos = $empleado->descuentos()->where('d_fecha', '>=', $event->periodo->inicio)
                ->where('d_fecha', '<=', $event->periodo->fin)
                ->sum('valor');
            $totalAbonos = $empleado->abonos()->where('a_fecha', '>=', $event->periodo->inicio)
                ->where('a_fecha', '<=', $event->periodo->fin)
                ->sum('valor');
    
            foreach ($empleado->prestamos as $prestamo) {
                try {
                    Log::info("Revisando préstamo ID: {$prestamo->id_prestamos}");
                    Log::info("Cuotas totales: {$prestamo->cuotas}, Cuota actual: {$prestamo->cuota_actual}");

                    if ($prestamo->cuota_actual >= $prestamo->cuotas) {
                        Log::warning("Préstamo {$prestamo->id_prestamos} tiene cuota_actual mayor que cuotas totales. Corrigiendo...");
                        $prestamo->cuota_actual = 0;
                        $prestamo->save();
                    }
    
                    $cuotaPendiente = $prestamo->cuotas()
                        ->where('pagada', false)
                        ->orderBy('created_at')
                        ->first();
    
                    if ($cuotaPendiente) {
                        DB::transaction(function () use ($cuotaPendiente, $prestamo, &$totalPrestamos) {
                            $cuotaPendiente->update(['pagada' => true]);
                            $prestamo->increment('cuota_actual');
                            $totalPrestamos += $cuotaPendiente->valor;
    
                            Log::info("Cuota {$cuotaPendiente->id_cuotas} procesada. Valor: {$cuotaPendiente->valor}");
                        });
                    }
                    
                } catch (\Exception $e) {
                    Log::error("Error procesando préstamo: " . $e->getMessage());
                }
            }
    
            Log::info('Total a descontar por préstamos: ' . $totalPrestamos);
            Log::info('Total final a pagar: ' . ($totalPagado - $totalPrestamos));
    
            $event->periodo->empleados()->attach($empleado->id_empleado, [
                'total_descuentos' => $totalDescuentos,
                'total_abonos' => $totalAbonos,
                'total_prestamos' => $totalPrestamos,
                'total_pagado' => $totalPagado - $totalPrestamos - $totalDescuentos + $totalAbonos
            ]);
    
            Log::info('Empleado asignado al periodo con sus cálculos');
        }
    
        Log::info('=== PROCESO COMPLETADO ===');
    }


    public function calcularFactor($frecuencia)
    {
        return match ($frecuencia) {
            1 => 0.5, // Quincenal
            2 => 1,   // Mensual
            3 => 0.25, // Semanal
            default => 0
        };
    }
}