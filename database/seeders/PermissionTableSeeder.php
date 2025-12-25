<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [

            // For roll and permission
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',

            // For Role and permission
            'role-and-permission-list',

            // For Resource
            'resource-list',

            // For User
            'user-list',
            'user-create',
            'user-edit',
            'user-delete',


            // Site Setting
            'site-setting',

            // Dashboard
            'login-log-list',

            //owner
            'owner',
            'owner-list',
            'owner-create',
            'owner-edit',
            'owner-delete',

            //owner bank
            'owner-bank-list',
            'owner-bank-create',
            'owner-bank-edit',
            'owner-bank-delete',

            //owner flat
            'owner-flat-list',
            'owner-flat-create',
            'owner-flat-edit',
            'owner-flat-delete',

        ];
        foreach ($permissions as $permission) {
            if (!Permission::where('name', $permission)->exists()) {
                Permission::create(['name' => $permission]);
            }
        }
    }
}
