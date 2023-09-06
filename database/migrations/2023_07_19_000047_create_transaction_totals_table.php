<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionTotalsTable extends Migration
{
    public function up()
    {
        Schema::create('transaction_totals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('total_invoice', 15, 2);
            $table->decimal('total_diskon', 15, 2);
            $table->decimal('total_adjustment', 15, 2);
            $table->decimal('total_retur', 15, 2);
            $table->decimal('total_bayar', 15, 2);
            $table->decimal('total_potongan', 15, 2);
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
