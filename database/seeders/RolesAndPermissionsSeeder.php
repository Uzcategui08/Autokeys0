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
        //
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $user = Role::firstOrCreate(['name' => 'user']);
        
        // Crear permisos
        Permission::firstOrCreate(['name' => 'crear_user']);
        Permission::firstOrCreate(['name' => 'editar_user']);
        Permission::firstOrCreate(['name' => 'ver_user']);
        
        // Asignar TODOS los permisos al rol admin
        $admin->syncPermissions(Permission::all());

                // Asignar rol admin a un usuario específico
                $user = User::where('rol', 'admin')->first();
                if ($user) {
                    $user->assignRole('admin');
                    $user->rol = 'admin';
                    $user->save();
                }
    }
}
