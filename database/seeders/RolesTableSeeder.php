<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            [
                'id'    => 1,
                'title' => 'Admin',
            ],
            [
                'id'    => 2,
                'title' => 'Penjualan',
            ],
            [
                'id'    => 3,
                'title' => 'Produksi',
            ],
            [
                'id'    => 4,
                'title' => 'Aquarium',
            ],
            [
                'id'    => 5,
                'title' => 'Direktur',
            ],

        ];

        Role::insert($roles);
    }
}
