<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ================
        // MENU GROUP
        // ================

        $groups = [
            [
                'name' => 'Dashboard',
                'permission_name' => 'dashboard',
                'icon' => 'bx bx-home-circle',
                'route' => 'dashboard',
                'order' => 1
            ],
            [
                'name' => 'Management Users',
                'permission_name' => 'users.manage',
                'icon' => 'bx bx-user',
                'route' => null,
                'order' => 2
            ],
            [
                'name' => 'Role Management',
                'permission_name' => 'roles.manage',
                'icon' => 'bx bx-shield-alt',
                'route' => null,
                'order' => 3
            ],
            [
                'name' => 'Menu Management',
                'permission_name' => 'menus.manage',
                'icon' => 'bx bx-menu',
                'route' => null,
                'order' => 4
            ],
            [
                'name' => 'Setting Management',
                'permission_name' => 'settings.manage',
                'icon' => 'bx bx-cog',
                'route' => null,
                'order' => 5
            ],
        ];


        DB::table('menu_groups')->insert($groups);


        $users         = DB::table('menu_groups')->where('name', 'Management Users')->first()->id;
        $roles         = DB::table('menu_groups')->where('name', 'Role Management')->first()->id;
        $menus         = DB::table('menu_groups')->where('name', 'Menu Management')->first()->id;
        $settings      = DB::table('menu_groups')->where('name', 'Setting Management')->first()->id;

        // ================
        // MENU ITEMS
        // ================

        $items = [
            // Management User
            [
                'name' => 'User List',
                'route' => 'users.index',
                'permission_name' => 'users.index',
                'menu_group_id' => $users,
                'order' => 1
            ],

            // Role Management
            [
                'name' => 'Role List',
                'route' => 'roles.index',
                'permission_name' => 'roles.index',
                'menu_group_id' => $roles,
                'order' => 1
            ],
            [
                'name' => 'Permission List',
                'route' => 'permissions.index',
                'permission_name' => 'permissions.index',
                'menu_group_id' => $roles,
                'order' => 2
            ],
            [
                'name' => 'Permission To Role',
                'route' => 'assign.index',
                'permission_name' => 'assign.index',
                'menu_group_id' => $roles,
                'order' => 3
            ],
            [
                'name' => 'User To Role',
                'route' => 'assign.user.index',
                'permission_name' => 'assign.user.index',
                'menu_group_id' => $roles,
                'order' => 4
            ],

            // Menu Management
            [
                'name' => 'Menu Group',
                'route' => 'menu-groups.index',
                'permission_name' => 'menu-groups.index',
                'menu_group_id' => $menus,
                'order' => 1
            ],

            // Setting Management
            [
                'name' => 'Log Activity List',
                'route' => 'log-activity.index',
                'permission_name' => 'log-activity.index',
                'menu_group_id' => $settings,
                'order' => 1
            ],
        ];

        DB::table('menu_items')->insert($items);
    }
}
