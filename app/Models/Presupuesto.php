<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Presupuesto
 *
 * @property $id_presupuesto
 * @property $id_cliente
 * @property $f_presupuesto
 * @property $validez
 * @property $descuento
 * @property $iva
 * @property $estado
 * @property $items
 * @property $created_at
 * @property $updated_at
 *
 * @property Cliente $cliente
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Presupuesto extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $primaryKey = 'id_presupuesto';
    protected $fillable = ['id_cliente', 'f_presupuesto', 'validez', 'descuento', 'iva', 'estado', 'items'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cliente()
    {
        return $this->belongsTo(\App\Models\Cliente::class, 'id_cliente', 'id_cliente');
    }
    
}
