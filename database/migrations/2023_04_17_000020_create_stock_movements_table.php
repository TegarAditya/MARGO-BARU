<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockMovementsTable extends Migration
{
    public function up()
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('movement_date');
            $table->string('movement_type');
            $table->float('quantity', 17, 2);
            $table->string('transaction_type')->nullable();
            $table->date('reference_date');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
