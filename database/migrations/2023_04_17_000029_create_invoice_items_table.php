<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceItemsTable extends Migration
{
    public function up()
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('quantity');
            $table->decimal('price', 15, 2);
            $table->decimal('total', 15, 2);
            $table->decimal('discount', 15, 2)->nullable();
            $table->decimal('total_discount', 15, 2);
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
