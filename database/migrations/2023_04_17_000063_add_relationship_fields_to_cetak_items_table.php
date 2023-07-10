<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToCetakItemsTable extends Migration
{
    public function up()
    {
        Schema::table('cetak_items', function (Blueprint $table) {
            $table->unsignedBigInteger('cetak_id')->nullable();
            $table->foreign('cetak_id', 'cetak_fk_8682435')->references('id')->on('cetaks');
            $table->unsignedBigInteger('semester_id')->nullable();
            $table->foreign('semester_id', 'semester_fk_8147723')->references('id')->on('semesters');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id', 'product_fk_8144974')->references('id')->on('book_variants');
            $table->unsignedBigInteger('halaman_id')->nullable();
            $table->foreign('halaman_id', 'halaman_fk_8144975')->references('id')->on('halamen');
            $table->unsignedBigInteger('plate_id')->nullable();
            $table->foreign('plate_id', 'plate_fk_8145063')->references('id')->on('materials');
            $table->unsignedBigInteger('paper_id')->nullable();
            $table->foreign('paper_id', 'paper_fk_8145066')->references('id')->on('materials');
        });
    }
}
