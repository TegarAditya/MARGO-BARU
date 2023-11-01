<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinishingMasuksTable extends Migration
{
    public function up()
    {
        Schema::create('finishing_masuks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('no_spk')->nullable();
            $table->date('date')->nullable();
            $table->integer('quantity');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
