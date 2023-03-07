<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToFinishingItemsTable extends Migration
{
    public function up()
    {
        Schema::table('finishing_items', function (Blueprint $table) {
            $table->unsignedBigInteger('semester_id')->nullable();
            $table->foreign('semester_id', 'semester_fk_8147803')->references('id')->on('semesters');
            $table->unsignedBigInteger('buku_id')->nullable();
            $table->foreign('buku_id', 'buku_fk_8145139')->references('id')->on('books');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id', 'product_fk_8145140')->references('id')->on('book_variants');
        });
    }
}
