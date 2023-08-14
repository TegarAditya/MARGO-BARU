<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductionPaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('production_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('no_payment')->unique();
            $table->date('date');
            $table->decimal('nominal', 15, 2);
            $table->string('payment_method')->nullable();
            $table->longText('note')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
