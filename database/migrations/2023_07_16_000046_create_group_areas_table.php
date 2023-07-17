<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupAreasTable extends Migration
{
    public function up()
    {
        Schema::create('group_areas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code')->nullable();
            $table->string('name');
            $table->string('provinsi');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
