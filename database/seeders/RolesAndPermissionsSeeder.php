<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;



class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear roles
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $user = Role::firstOrCreate(['name' => 'user']);
      //  $limitedUser = Role::firstOrCreate(['name' => 'limited_user']); // Nuevo rol
    
        // Permisos generales
        Permission::firstOrCreate(['name' => 'crear_user']);
        Permission::firstOrCreate(['name' => 'editar_user']);
        Permission::firstOrCreate(['name' => 'ver_user']);
    
        // Permisos específicos para limited_user
      //  Permission::firstOrCreate(['name' => 'presupuestos_limited']);
        //Permission::firstOrCreate(['name' => 'ordenes_limited']);
        //Permission::firstOrCreate(['name' => 'ventas_limited']);
        //Permission::firstOrCreate(['name' => 'inventario_limited']);
        //Permission::firstOrCreate(['name' => 'costos_gastos_limited']);
    
        // Asignar TODOS los permisos al rol admin
        $admin->syncPermissions(Permission::all());
    
        // Asignar permisos limitados
      /*  $limitedUser->syncPermissions([
            'presupuestos_limited',
            'ordenes_limited',
            'ventas_limited',
            'inventario_limited',
            'costos_gastos_limited'
        ]);
    */
        // Asignar rol admin a un usuario específico
        $adminUser = User::where('name', 'admin')->first();
        if ($adminUser) {
            $adminUser->assignRole('admin');
            $adminUser->rol = 'admin';
            $adminUser->save();
        }
        // Obtener el usuario
//$limitedUser = User::find(2);

// Obtener el rol
//$role = Role::where('name', 'limited_user')->first();

// Asignar el rol correctamente
//$limitedUser->syncRoles([$role]);

// También actualizar el campo 'rol' si lo usas
//$limitedUser->update(['rol' => 'limited_user']);
    }
}
