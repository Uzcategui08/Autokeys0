<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Pnomina
 *
 * @property $id_pnomina
 * @property $id_tnomina
 * @property $inicio
 * @property $fin
 * @property $activo
 * @property $created_at
 * @property $updated_at
 *
 * @property Tnomina $tnomina
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Pnomina extends Model
{
    
    protected $perPage = 20;
    protected $primaryKey = 'id_pnomina';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['id_pnomina', 'id_tnomina', 'inicio', 'fin', 'activo'];

    protected $casts = [
        'inicio' => 'datetime',
        'fin' => 'datetime'
    ];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tnomina()
    {
        return $this->belongsTo(\App\Models\Tnomina::class, 'id_tnomina', 'id_tnomina');
    }

    public function tipo()
    {
        return $this->belongsTo(Tnomina::class, 'id_tnomina', 'id_tnomina');
    }

    public function empleados(): BelongsToMany
    {
        return $this->belongsToMany(Empleado::class, 'nempleados', 'id_pnomina', 'id_empleado')
            ->withPivot('total_descuentos', 'total_abonos', 'total_prestamos', 'total_costos', 'total_pagado');
    }

    public function nempleados()
    {
        return $this->hasMany(Nempleado::class, 'id_pnomina');
    }

    
}
