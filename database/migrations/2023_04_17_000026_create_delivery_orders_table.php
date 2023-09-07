<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('delivery_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('no_suratjalan')->unique();
            $table->date('date');
            $table->longText('address')->nullable();
            $table->boolean('faktur')->default(0)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
