<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToDeliveryOrderItemsTable extends Migration
{
    public function up()
    {
        Schema::table('delivery_order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('semester_id')->nullable();
            $table->foreign('semester_id', 'semester_fk_8114655')->references('id')->on('semesters');
            $table->unsignedBigInteger('salesperson_id')->nullable();
            $table->foreign('salesperson_id', 'salesperson_fk_8114656')->references('id')->on('salespeople');
            $table->unsignedBigInteger('sales_order_id')->nullable();
            $table->foreign('sales_order_id', 'sales_order_fk_8116182')->references('id')->on('sales_orders');
            $table->unsignedBigInteger('delivery_order_id')->nullable();
            $table->foreign('delivery_order_id', 'delivery_order_fk_8114657')->references('id')->on('delivery_orders');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id', 'product_fk_8114658')->references('id')->on('book_variants');
        });
    }
}
