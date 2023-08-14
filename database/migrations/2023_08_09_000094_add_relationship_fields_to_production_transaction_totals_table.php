<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToProductionTransactionTotalsTable extends Migration
{
    public function up()
    {
        Schema::table('production_transaction_totals', function (Blueprint $table) {
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->foreign('vendor_id', 'vendor_fk_8854865')->references('id')->on('vendors');
        });
    }
}
