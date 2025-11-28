<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Devolucion extends Model
{
    use HasFactory;

    protected $table = 'devoluciones';

    protected $fillable = [
        'fecha',
        'monto',
        'descripcion',
    ];

    protected $casts = [
        'fecha' => 'date',
        'monto' => 'decimal:2',
    ];
}
