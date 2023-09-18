<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'id'             => 1,
                'name'           => 'Admin',
                'email'          => 'admin@admin.com',
                'password'       => bcrypt('password'),
                'remember_token' => null,
            ],
            [
                'id'             => 2,
                'name'           => 'Ningrum',
                'email'          => 'ningrum@margomitrojoyo.online',
                'password'       => bcrypt('password'),
                'remember_token' => null,
            ],
            [
                'id'             => 3,
                'name'           => 'Nina',
                'email'          => 'nina@margomitrojoyo.online',
                'password'       => bcrypt('password'),
                'remember_token' => null,
            ],
            [
                'id'             => 4,
                'name'           => 'Lisa',
                'email'          => 'lisa@margomitrojoyo.online',
                'password'       => bcrypt('password'),
                'remember_token' => null,
            ],
            [
                'id'             => 5,
                'name'           => 'Mas Gun',
                'email'          => 'masgun@margomitrojoyo.online',
                'password'       => bcrypt('password'),
                'remember_token' => null,
            ],
            [
                'id'             => 6,
                'name'           => 'Tika',
                'email'          => 'tika@margomitrojoyo.online',
                'password'       => bcrypt('password'),
                'remember_token' => null,
            ],
            [
                'id'             => 7,
                'name'           => 'Angputranto',
                'email'          => 'ang@margomitrojoyo.online',
                'password'       => bcrypt('password'),
                'remember_token' => null,
            ],
            [
                'id'             => 8,
                'name'           => 'Pak Dirman',
                'email'          => 'direktur@margomitrojoyo.online',
                'password'       => bcrypt('password'),
                'remember_token' => null,
            ],
        ];

        User::insert($users);
    }
}
