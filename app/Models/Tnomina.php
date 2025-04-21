<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Tnomina
 *
 * @property $id_nomina
 * @property $nombre
 * @property $frecuencia
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Tnomina extends Model
{
    
    protected $perPage = 20;
    protected $primaryKey = 'id_tnomina';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['id_tnomina', 'nombre', 'frecuencia'];

    

}
