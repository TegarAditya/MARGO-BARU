<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MarketingArea;

class MarketingAreaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [
            [
                'name'  => 'RIAU',
            ],
            [
                'name'  => 'CIREBON',
            ],
            [
                'name'  => 'PONOROGO',
            ],
            [
                'name'  => 'PROBOLINGGO',
            ],
            [
                'name'  => 'TRENGGALEK',
            ],
            [
                'name'  => 'SERANG',
            ],
            [
                'name'  => 'JAKARTA',
            ],
            [
                'name'  => 'SOLO',
            ],
            [
                'name'  => 'BREBES',
            ],
            [
                'name'  => 'TANGERANG',
            ],
            [
                'name'  => 'PEKALONGAN',
            ],
            [
                'name'  => 'SUBANG',
            ],
            [
                'name'  => 'MEDAN',
            ],
            [
                'name'  => 'SIDOARJO',
            ],
            [
                'name'  => 'BEKASI',
            ],
            [
                'name'  => 'BOJONEGORO',
            ],
            [
                'name'  => 'SRAGEN',
            ],
            [
                'name'  => 'BANYUMAS',
            ],
            [
                'name'  => 'PACITAN',
            ],
            [
                'name'  => 'LAMPUNG',
            ],
            [
                'name'  => 'BOYOLALI',
            ],
            [
                'name'  => 'MOJOKERTO',
            ],
            [
                'name'  => 'CIANJUR',
            ],
            [
                'name'  => 'TANJUNG PINANG',
            ],
            [
                'name'  => 'KEDIRI',
            ],
            [
                'name'  => 'TASIKMALAYA',
            ],
            [
                'name'  => 'BANGLI',
            ],
        ];

        MarketingArea::insert($cities);
    }
}
