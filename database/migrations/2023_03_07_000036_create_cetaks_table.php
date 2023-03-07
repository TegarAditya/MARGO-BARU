<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCetaksTable extends Migration
{
    public function up()
    {
        Schema::create('cetaks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('no_spc')->unique();
            $table->date('date');
            $table->string('type');
            $table->decimal('total_cost', 15, 2);
            $table->float('total_oplah', 15, 2);
            $table->longText('note')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
