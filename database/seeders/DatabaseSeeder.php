<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            PermissionsTableSeeder::class,
            RolesTableSeeder::class,
            PermissionRoleTableSeeder::class,
            UsersTableSeeder::class,
            RoleUserTableSeeder::class,
            MarketingAreaTableSeeder::class,
            SemesterTableSeeder::class,
            UnitTableSeeder::class,
            WarehouseTableSeeder::class
        ]);
    }
}
