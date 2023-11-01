<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToFinishingMasuksTable extends Migration
{
    public function up()
    {
        Schema::table('finishing_masuks', function (Blueprint $table) {
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->foreign('vendor_id', 'vendor_fk_9162965')->references('id')->on('vendors');
            $table->unsignedBigInteger('semester_id')->nullable();
            $table->foreign('semester_id', 'semester_fk_9166780')->references('id')->on('semesters');
            $table->unsignedBigInteger('finishing_item_id')->nullable();
            $table->foreign('finishing_item_id', 'finishing_item_fk_9162966')->references('id')->on('finishing_items');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id', 'product_fk_9166781')->references('id')->on('book_variants');
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->foreign('created_by_id', 'created_by_fk_9162967')->references('id')->on('users');
            $table->unsignedBigInteger('updated_by_id')->nullable();
            $table->foreign('updated_by_id', 'updated_by_fk_9162968')->references('id')->on('users');
        });
    }
}
