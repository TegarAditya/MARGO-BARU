<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHalamenTable extends Migration
{
    public function up()
    {
        Schema::create('halamen', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code')->unique();
            $table->string('name');
            $table->integer('value');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
