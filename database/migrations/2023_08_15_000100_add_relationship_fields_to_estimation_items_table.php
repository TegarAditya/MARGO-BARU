<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToEstimationItemsTable extends Migration
{
    public function up()
    {
        Schema::table('estimation_items', function (Blueprint $table) {
            $table->unsignedBigInteger('estimation_id')->nullable();
            $table->foreign('estimation_id', 'estimation_fk_8878692')->references('id')->on('estimations');
            $table->unsignedBigInteger('semester_id')->nullable();
            $table->foreign('semester_id', 'semester_fk_8878693')->references('id')->on('semesters');
            $table->unsignedBigInteger('salesperson_id')->nullable();
            $table->foreign('salesperson_id', 'salesperson_fk_8878694')->references('id')->on('salespeople');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id', 'product_fk_8878696')->references('id')->on('book_variants');
            $table->unsignedBigInteger('jenjang_id')->nullable();
            $table->foreign('jenjang_id', 'jenjang_fk_8878697')->references('id')->on('jenjangs');
            $table->unsignedBigInteger('kurikulum_id')->nullable();
            $table->foreign('kurikulum_id', 'kurikulum_fk_8878698')->references('id')->on('kurikulums');
        });
    }
}
