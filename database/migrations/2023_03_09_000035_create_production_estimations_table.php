<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductionEstimationsTable extends Migration
{
    public function up()
    {
        Schema::create('production_estimations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('quantity');
            $table->integer('estimasi')->nullable();
            $table->integer('isi')->nullable();
            $table->integer('cover')->nullable();
            $table->integer('finishing')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
