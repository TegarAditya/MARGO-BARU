<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToProductionEstimationsTable extends Migration
{
    public function up()
    {
        Schema::table('production_estimations', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id', 'product_fk_8144003')->references('id')->on('book_variants');
        });
    }
}
