<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        /*
    |--------------------------------------------------------------------------
    | MENU GROUP PERMISSIONS
    |--------------------------------------------------------------------------
    */
        Permission::create(['name' => 'dashboard']);
        Permission::create(['name' => 'users.manage']);
        Permission::create(['name' => 'roles.manage']);
        Permission::create(['name' => 'menus.manage']);
        Permission::create(['name' => 'settings.manage']);


        /*
    |--------------------------------------------------------------------------
    | USER MODULE
    |--------------------------------------------------------------------------
    */
        Permission::create(['name' => 'users.index']);
        Permission::create(['name' => 'users.create']);
        Permission::create(['name' => 'users.edit']);
        Permission::create(['name' => 'users.destroy']);
        Permission::create(['name' => 'users.import']);
        Permission::create(['name' => 'users.export']);


        /*
    |--------------------------------------------------------------------------
    | ROLE MODULE
    |--------------------------------------------------------------------------
    */
        Permission::create(['name' => 'roles.index']);
        Permission::create(['name' => 'roles.create']);
        Permission::create(['name' => 'roles.edit']);
        Permission::create(['name' => 'roles.destroy']);
        Permission::create(['name' => 'roles.import']);
        Permission::create(['name' => 'roles.export']);


        /*
    |--------------------------------------------------------------------------
    | PERMISSION MODULE
    |--------------------------------------------------------------------------
    | diperbaiki -> sebelumnya salah: permission.index menjadi permissions.index
    */
        Permission::create(['name' => 'permissions.index']);
        Permission::create(['name' => 'permissions.create']);
        Permission::create(['name' => 'permissions.edit']);
        Permission::create(['name' => 'permissions.destroy']);
        Permission::create(['name' => 'permissions.import']);
        Permission::create(['name' => 'permissions.export']);


        /*
    |--------------------------------------------------------------------------
    | ROLE ASSIGNMENT (Permission to Role)
    |--------------------------------------------------------------------------
    | disesuaikan -> menu memakai permissions: roles.permissions
    */
        Permission::create(['name' => 'assign.index']);
        Permission::create(['name' => 'assign.create']);
        Permission::create(['name' => 'assign.edit']);
        Permission::create(['name' => 'assign.destroy']);

        /*
    |--------------------------------------------------------------------------
    | USER ASSIGNMENT (User to Role)
    |--------------------------------------------------------------------------
    */
        Permission::create(['name' => 'assign.user.index']);
        Permission::create(['name' => 'assign.user.create']);
        Permission::create(['name' => 'assign.user.edit']);


        /*
    |--------------------------------------------------------------------------
    | MENU GROUP CRUD
    |--------------------------------------------------------------------------
    | disesuaikan -> menu: menu-groups.index
    */
        Permission::create(['name' => 'menu-groups.index']);
        Permission::create(['name' => 'menu-groups.create']);
        Permission::create(['name' => 'menu-groups.edit']);
        Permission::create(['name' => 'menu-groups.destroy']);


        /*
    |--------------------------------------------------------------------------
    | MENU ITEM CRUD
    |--------------------------------------------------------------------------
    | disesuaikan -> menu: menu-items.index
    */
        Permission::create(['name' => 'menu-items.index']);
        Permission::create(['name' => 'menu-items.create']);
        Permission::create(['name' => 'menu-items.edit']);
        Permission::create(['name' => 'menu-items.destroy']);


        /*
    |--------------------------------------------------------------------------
    | SETTING MODULE
    |--------------------------------------------------------------------------
    | disesuaikan -> route: settings-logs.index
    */
        Permission::create(['name' => 'log-activity.index']);


        /*
    |--------------------------------------------------------------------------
    | ROLES
    |--------------------------------------------------------------------------
    */

        // USER ROLE
        $roleUser = Role::create(['name' => 'user']);
        $roleUser->givePermissionTo([
            'dashboard',
            'users.index',
        ]);

        // SUPER ADMIN
        $role = Role::create(['name' => 'super-admin']);
        $role->givePermissionTo(Permission::all());


        /*
    |--------------------------------------------------------------------------
    | ASSIGN ROLE TO INITIAL USERS
    |--------------------------------------------------------------------------
    */
        if ($user = User::find(1)) {
            $user->assignRole('super-admin');
        }

        if ($user = User::find(2)) {
            $user->assignRole('user');
        }
    }
}
