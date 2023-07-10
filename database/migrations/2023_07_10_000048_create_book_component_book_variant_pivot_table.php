<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookComponentBookVariantPivotTable extends Migration
{
    public function up()
    {
        Schema::create('book_component_book_variant', function (Blueprint $table) {
            $table->unsignedBigInteger('book_component_id');
            $table->foreign('book_component_id', 'book_component_id_fk_8736099')->references('id')->on('book_components')->onDelete('cascade');
            $table->unsignedBigInteger('book_variant_id');
            $table->foreign('book_variant_id', 'book_variant_id_fk_8736099')->references('id')->on('book_variants')->onDelete('cascade');
        });
    }
}
