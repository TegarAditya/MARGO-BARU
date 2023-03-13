<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToInvoiceItemsTable extends Migration
{
    public function up()
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->foreign('invoice_id', 'invoice_fk_8116079')->references('id')->on('invoices');
            $table->unsignedBigInteger('delivery_order_id')->nullable();
            $table->foreign('delivery_order_id', 'delivery_order_fk_8116080')->references('id')->on('delivery_orders');
            $table->unsignedBigInteger('delivery_order_item_id')->nullable();
            $table->foreign('delivery_order_item_id', 'delivery_order_item_fk_8116081')->references('id')->on('delivery_order_items');
            $table->unsignedBigInteger('semester_id')->nullable();
            $table->foreign('semester_id', 'semester_fk_8116082')->references('id')->on('semesters');
            $table->unsignedBigInteger('salesperson_id')->nullable();
            $table->foreign('salesperson_id', 'salesperson_fk_8116083')->references('id')->on('salespeople');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id', 'product_fk_8116084')->references('id')->on('book_variants');
        });
    }
}
