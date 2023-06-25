<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('no_kwitansi')->unique();
            $table->date('date');
            $table->decimal('paid', 15, 2);
            $table->decimal('discount', 15, 2)->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('payment_method');
            $table->longText('note')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
