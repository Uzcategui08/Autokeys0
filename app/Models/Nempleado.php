<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Nempleado
 *
 * @property $id_nempleado
 * @property $id_pnomina
 * @property $id_empleado
 * @property $total_descuentos
 * @property $total_abonos
 * @property $total_prestamos
 * @property $total_pagado
 * @property $created_at
 * @property $updated_at
 *
 * @property Empleado $empleado
 * @property Pnomina $pnomina
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Nempleado extends Model
{
    
    protected $perPage = 20;
    protected $primaryKey = 'id_nempleado';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['id_nempleado', 'id_pnomina', 'id_empleado', 'total_descuentos', 'total_abonos', 'total_prestamos', 'total_costos', 'total_pagado', 'metodo_pago'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function empleado()
    {
        return $this->belongsTo(\App\Models\Empleado::class, 'id_empleado', 'id_empleado');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pnomina()
    {
        return $this->belongsTo(\App\Models\Pnomina::class, 'id_pnomina', 'id_pnomina');
    }
    
    public function periodo()
    {
        return $this->belongsTo(Pnomina::class, 'id_pnomina');
    }

    public function tnomina()
    {
        return $this->belongsTo(\App\Models\Tnomina::class, 'id_tnomina', 'id_tnomina');
    }

    
}
