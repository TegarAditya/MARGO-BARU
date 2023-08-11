<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryPlatesTable extends Migration
{
    public function up()
    {
        Schema::create('delivery_plates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('no_suratjalan')->unique();
            $table->date('date');
            $table->string('customer')->nullable();
            $table->longText('note')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
