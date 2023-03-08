<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToSalespeopleTable extends Migration
{
    public function up()
    {
        Schema::table('salespeople', function (Blueprint $table) {
            $table->unsignedBigInteger('marketing_area_id')->nullable();
            $table->foreign('marketing_area_id', 'marketing_area_fk_8102470')->references('id')->on('marketing_areas');
        });
    }
}
