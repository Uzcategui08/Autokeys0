<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Inventario
 *
 * @property $id_inventario
 * @property $id_producto
 * @property $id_almacen
 * @property $cantidad
 * @property $created_at
 * @property $updated_at
 *
 * @property Almacene $almacene
 * @property Producto $producto
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Inventario extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['id_inventario', 'id_producto', 'id_almacen', 'cantidad'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function almacene()
    {
        return $this->belongsTo(\App\Models\Almacene::class, 'id_almacen', 'id_almacen');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function producto()
    {
        return $this->belongsTo(\App\Models\Producto::class, 'id_producto', 'id_producto');
    }
    
}
