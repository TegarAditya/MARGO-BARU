<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinishingsTable extends Migration
{
    public function up()
    {
        Schema::create('finishings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('no_spk');
            $table->date('date');
            $table->decimal('total_cost', 15, 2);
            $table->string('total_oplah');
            $table->longText('note')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
