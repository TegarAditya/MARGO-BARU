<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $units = [
            [
                'code'  => 'eks',
                'name'  => 'Eksemplar',
            ],
            [
                'code'  => 'lbr',
                'name'  => 'Lembar',
            ],
            [
                'code'  => 'ltr',
                'name'  => 'Liter',
            ],
            [
                'code'  => 'kg',
                'name'  => 'Kilogram',
            ],
        ];

        Unit::insert($units);
    }
}
