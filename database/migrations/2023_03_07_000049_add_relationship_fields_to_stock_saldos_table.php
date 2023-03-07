<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToStockSaldosTable extends Migration
{
    public function up()
    {
        Schema::table('stock_saldos', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id', 'product_fk_8109523')->references('id')->on('book_variants');
            $table->unsignedBigInteger('material_id')->nullable();
            $table->foreign('material_id', 'material_fk_8109524')->references('id')->on('materials');
        });
    }
}
