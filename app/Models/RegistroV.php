<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class RegistroV
 *
 * @property $id
 * @property $fecha_h
 * @property $tecnico
 * @property $trabajo
 * @property $cliente
 * @property $telefono
 * @property $valor_v
 * @property $estatus
 * @property $metodo_p
 * @property $titular_c
 * @property $pagos
 * @property $descripcion_ce
 * @property $monto_ce
 * @property $metodo_pce
 * @property $porcentaje_c
 * @property $marca
 * @property $modelo
 * @property $año
 * @property $items
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class RegistroV extends Model
{

    protected $perPage = 20;

    protected $table = 'registroV'; // or whatever your actual table name is
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['fecha_h', 'tecnico', 'trabajo', 'cliente', 'telefono', 'valor_v', 'estatus', 'metodo_p', 'titular_c', 'pagos', 'descripcion_ce', 'monto_ce', 'metodo_pce', 'porcentaje_c', 'marca', 'modelo', 'año', 'items'];

    protected $casts = [
        'pagos' => 'array',
        'fecha_h' => 'datetime'
    ];

    public function setPagosAttribute($value)
    {
        $this->attributes['pagos'] = is_array($value) ? json_encode($value) : $value;
    }

    public function getPagosAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    public function inventarios()
    {
        return $this->hasMany(Inventario::class, 'id_producto', 'id_producto');
    }

    public function registroVs()
    {
        return $this->belongsTo(RegistroV::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
