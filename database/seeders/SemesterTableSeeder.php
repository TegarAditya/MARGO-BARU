<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Semester;

class SemesterTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $semesters = [
            [
                'code'  => '0219',
                'name'  => 'SEMESTER GENAP 2019/2020',
                'type' => 'genap',
                'start_date'  => "2019-10-01",
                'end_date'  => "2020-01-31",
                'status' => 0
            ],
            [
                'code'  => '0120',
                'name'  => 'SEMESTER GANJIL 2020/2021',
                'type' => 'ganjil',
                'start_date'  => "2020-04-01",
                'end_date'  => "2020-07-01",
                'status' => 0
            ],
            [
                'code'  => '0220',
                'name'  => 'SEMESTER GENAP 2020/2021',
                'type' => 'genap',
                'start_date'  => "2020-09-01",
                'end_date'  => "2021-01-31",
                'status' => 0
            ],
            [
                'code'  => '0121',
                'name'  => 'SEMESTER GANJIL 2021/2022',
                'type' => 'ganjil',
                'start_date'  => "2021-07-01",
                'end_date'  => "2021-09-08",
                'status' => 0
            ],
            [
                'code'  => '0221',
                'name'  => 'SEMESTER GENAP 2021/2022',
                'type' => 'genap',
                'start_date'  => "2021-10-01",
                'end_date'  => "2022-01-31",
                'status' => 0
            ],
            [
                'code'  => '0122',
                'name'  => 'SEMESTER GANJIL 2022/2023',
                'type' => 'ganjil',
                'start_date'  => "2022-02-01",
                'end_date'  => "2022-08-31",
                'status' => 0
            ],
            [
                'code'  => '0222',
                'name'  => 'SEMESTER GENAP 2022/2023',
                'type' => 'genap',
                'start_date'  => "2022-09-08",
                'end_date'  => "2023-01-31",
                'status' => 0
            ],
            [
                'code'  => '0123',
                'name'  => 'SEMESTER GANJIL 2023/2024',
                'type' => 'ganjil',
                'start_date'  => "2023-02-01",
                'end_date'  => "2023-08-31",
                'status' => 1
            ],
            [
                'code'  => '0223',
                'name'  => 'SEMESTER GENAP 2023/2024',
                'type' => 'genap',
                'start_date'  => "2023-09-08",
                'end_date'  => "2024-01-31",
                'status' => 1
            ],
            [
                'code'  => '0124',
                'name'  => 'SEMESTER GANJIL 2024/2025',
                'type' => 'ganjil',
                'start_date'  => "2024-02-01",
                'end_date'  => "2024-08-31",
                'status' => 1
            ],
            [
                'code'  => '0224',
                'name'  => 'SEMESTER GENAP 2024/2025',
                'type' => 'genap',
                'start_date'  => "2024-09-08",
                'end_date'  => "2025-01-31",
                'status' => 1
            ],
        ];
        Semester::insert($semesters);
    }
}
