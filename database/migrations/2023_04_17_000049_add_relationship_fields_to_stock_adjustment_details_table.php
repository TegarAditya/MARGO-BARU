<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToStockAdjustmentDetailsTable extends Migration
{
    public function up()
    {
        Schema::table('stock_adjustment_details', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id', 'product_fk_8109420')->references('id')->on('book_variants');
            $table->unsignedBigInteger('material_id')->nullable();
            $table->foreign('material_id', 'material_fk_8109421')->references('id')->on('materials');
            $table->unsignedBigInteger('stock_adjustment_id')->nullable();
            $table->foreign('stock_adjustment_id', 'stock_adjustment_fk_8109422')->references('id')->on('stock_adjustments');
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->foreign('created_by_id')->references('id')->on('users');
            $table->unsignedBigInteger('updated_by_id')->nullable();
            $table->foreign('updated_by_id')->references('id')->on('users');
        });
    }
}
