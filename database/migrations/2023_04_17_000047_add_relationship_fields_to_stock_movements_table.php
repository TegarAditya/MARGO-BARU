<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToStockMovementsTable extends Migration
{
    public function up()
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->foreign('warehouse_id', 'warehouse_fk_8106901')->references('id')->on('warehouses');
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id', 'product_fk_8106904')->references('id')->on('book_variants');
            $table->unsignedBigInteger('material_id')->nullable();
            $table->foreign('material_id', 'material_fk_8108915')->references('id')->on('materials');
            $table->unsignedBigInteger('reversal_of_id')->nullable();
            $table->foreign('reversal_of_id', 'reversal_of_fk_8106961')->references('id')->on('stock_movements');
        });
    }
}
