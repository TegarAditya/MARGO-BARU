<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToMarketingAreasTable extends Migration
{
    public function up()
    {
        Schema::table('marketing_areas', function (Blueprint $table) {
            $table->unsignedBigInteger('group_area_id')->nullable();
            $table->foreign('group_area_id', 'group_area_fk_8756606')->references('id')->on('group_areas');
        });
    }
}
