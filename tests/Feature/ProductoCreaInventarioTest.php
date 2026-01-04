<?php

use App\Models\Almacene;
use App\Models\Inventario;
use App\Models\Producto;

it('crea registros de inventario en 0 al crear un producto', function () {
  $almacen1 = Almacene::create(['nombre' => 'Principal']);
  $almacen2 = Almacene::create(['nombre' => 'Secundario']);

  $producto = Producto::create([
    'id_producto' => 1001,
    'item' => 'Producto Test',
    'marca' => 'Marca',
    't_llave' => 'Tipo',
    'sku' => 'SKU-TEST-001',
    'precio' => 10.50,
  ]);

  expect(Inventario::where('id_producto', $producto->id_producto)->count())->toBe(2);

  $inventario1 = Inventario::where('id_producto', $producto->id_producto)
    ->where('id_almacen', $almacen1->id_almacen)
    ->first();

  $inventario2 = Inventario::where('id_producto', $producto->id_producto)
    ->where('id_almacen', $almacen2->id_almacen)
    ->first();

  expect($inventario1)->not->toBeNull();
  expect($inventario1->cantidad)->toBe(0);

  expect($inventario2)->not->toBeNull();
  expect($inventario2->cantidad)->toBe(0);
});
