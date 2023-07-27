<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlatePrintsTable extends Migration
{
    public function up()
    {
        Schema::create('plate_prints', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('no_spk')->unique();
            $table->date('date');
            $table->string('type');
            $table->longText('note')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
