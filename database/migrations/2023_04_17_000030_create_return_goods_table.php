<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReturnGoodsTable extends Migration
{
    public function up()
    {
        Schema::create('return_goods', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('no_retur')->unique();
            $table->date('date');
            $table->decimal('nominal', 15, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
