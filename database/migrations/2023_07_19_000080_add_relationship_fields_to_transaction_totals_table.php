<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToTransactionTotalsTable extends Migration
{
    public function up()
    {
        Schema::table('transaction_totals', function (Blueprint $table) {
            $table->unsignedBigInteger('salesperson_id')->nullable();
            $table->foreign('salesperson_id', 'salesperson_fk_8771305')->references('id')->on('salespeople');
        });
    }
}
