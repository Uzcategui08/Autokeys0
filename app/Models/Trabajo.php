<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Trabajo
 *
 * @property $id_trabajo
 * @property $nombre
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Trabajo extends Model
{
    
    protected $perPage = 20;
    protected $primaryKey = 'id_trabajo';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['id_trabajo', 'nombre'];


}
