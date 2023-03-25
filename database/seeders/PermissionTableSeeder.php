<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        

        $permissions = [
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',
            'product-list',
            'product-create',
            'product-edit',
            'product-delete',
            'categorie-list',
            'categorie-create',
            'categorie-edit',
            'categorie-delete',
            'user-list',
            'user-create',
            'user-edit',
            'user-delete'
        ];
       
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

      

        $admin = 'admin';
        $commercial = 'commercial';
        $user = 'user';

        Role::create(['name' => $admin])->givePermissionTo(Permission::all());

        Role::create(['name' => $commercial])->givePermissionTo([
            array_slice($permissions, 4, 11)
        ]);

        Role::create(['name' => $user])->givePermissionTo([
            $permissions[4]
        ]);
        
    }
}
