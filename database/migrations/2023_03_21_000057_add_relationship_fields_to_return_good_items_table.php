<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToReturnGoodItemsTable extends Migration
{
    public function up()
    {
        Schema::table('return_good_items', function (Blueprint $table) {
            $table->unsignedBigInteger('retur_id')->nullable();
            $table->foreign('retur_id', 'retur_fk_8116171')->references('id')->on('return_goods');
            $table->unsignedBigInteger('salesperson_id')->nullable();
            $table->foreign('salesperson_id', 'salesperson_fk_8116172')->references('id')->on('salespeople');
            $table->unsignedBigInteger('semester_id')->nullable();
            $table->foreign('semester_id', 'semester_fk_8116173')->references('id')->on('semesters');
            $table->unsignedBigInteger('sales_order_id')->nullable();
            $table->foreign('sales_order_id', 'sales_order_fk_8116183')->references('id')->on('sales_orders');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id', 'product_fk_8116175')->references('id')->on('book_variants');
        });
    }
}
