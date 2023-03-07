<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToAddressesTable extends Migration
{
    public function up()
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->unsignedBigInteger('salesperson_id')->nullable();
            $table->foreign('salesperson_id', 'salesperson_fk_8102480')->references('id')->on('salespeople');
            $table->unsignedBigInteger('marketing_area_id')->nullable();
            $table->foreign('marketing_area_id', 'marketing_area_fk_8102481')->references('id')->on('marketing_areas');
        });
    }
}
