<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('no_order')->nullable();
            $table->string('payment_type');
            $table->integer('quantity');
            $table->integer('moved')->default(0);
            $table->integer('retur')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
