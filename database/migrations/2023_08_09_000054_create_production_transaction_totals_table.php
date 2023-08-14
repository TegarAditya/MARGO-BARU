<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductionTransactionTotalsTable extends Migration
{
    public function up()
    {
        Schema::create('production_transaction_totals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('total_fee', 15, 2);
            $table->decimal('total_payment', 15, 2);
            $table->decimal('outstanding_fee', 15, 2);
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
