<?php

namespace App\Observers;

use App\Models\Almacene;
use App\Models\Inventario;
use App\Models\Producto;

class ProductoObserver
{
  /**
   * Cuando se crea un producto, inicializa su inventario en 0.
   */
  public function created(Producto $producto): void
  {
    $almacenIds = Almacene::query()->pluck('id_almacen');

    if ($almacenIds->isEmpty()) {
      return;
    }

    foreach ($almacenIds as $almacenId) {
      Inventario::firstOrCreate(
        [
          'id_producto' => $producto->id_producto,
          'id_almacen' => $almacenId,
        ],
        [
          'cantidad' => 0,
        ]
      );
    }
  }
}
