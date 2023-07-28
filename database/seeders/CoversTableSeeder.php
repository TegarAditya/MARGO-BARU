<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Cover;
use App\Models\Setting;

class CoversTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Cover::create( [
        'id'=>1,
        'code'=>'MMJ',
        'name'=>'MARGO MITRO JOYO',
        'created_at'=>NULL,
        'updated_at'=>NULL,
        'deleted_at'=>NULL
        ] );

        Cover::create( [
        'id'=>2,
        'code'=>'MMP',
        'name'=>'MATRA MEDIA PRESINDO',
        'created_at'=>NULL,
        'updated_at'=>NULL,
        'deleted_at'=>NULL
        ] );

        Cover::create( [
        'id'=>3,
        'code'=>'SRG',
        'name'=>'MGMP SRAGEN',
        'created_at'=>NULL,
        'updated_at'=>NULL,
        'deleted_at'=>NULL
        ] );

        Cover::create( [
        'id'=>4,
        'code'=>'CLP',
        'name'=>'MGMP CILACAP',
        'created_at'=>NULL,
        'updated_at'=>NULL,
        'deleted_at'=>NULL
        ] );

        Cover::create( [
        'id'=>5,
        'code'=>'BGR',
        'name'=>'MGMP BOGOR',
        'created_at'=>NULL,
        'updated_at'=>NULL,
        'deleted_at'=>NULL
        ] );

        Cover::create( [
        'id'=>6,
        'code'=>'JRA',
        'name'=>'JUARA',
        'created_at'=>NULL,
        'updated_at'=>NULL,
        'deleted_at'=>NULL
        ] );

        Cover::create( [
        'id'=>7,
        'code'=>'SPT',
        'name'=>'SIPINTAR',
        'created_at'=>NULL,
        'updated_at'=>NULL,
        'deleted_at'=>NULL
        ] );

        Cover::create( [
        'id'=>8,
        'code'=>'PDW',
        'name'=>'PANDAWA',
        'created_at'=>NULL,
        'updated_at'=>NULL,
        'deleted_at'=>NULL
        ] );

        Setting::create( [
            'key'=>'current_semester',
            'value'=>'11',
            'is_json'=> 0,
            'created_by_id' => 1,
            'updated_by_id' => 1,
        ] );
    }
}
