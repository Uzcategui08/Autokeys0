<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Cuota
 *
 * @property $id_cuotas
 * @property $id_prestamos
 * @property $valor
 * @property $fecha_vencimiento
 * @property $pagada
 * @property $created_at
 * @property $updated_at
 *
 * @property Prestamo $prestamo
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Cuota extends Model
{
    
    protected $perPage = 20;
    protected $primaryKey = 'id_cuotas';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['id_cuotas', 'id_prestamos', 'valor', 'fecha_vencimiento', 'pagada'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function prestamo()
    {
        return $this->belongsTo(\App\Models\Prestamo::class, 'id_prestamos', 'id_prestamos');
    }
    
}
