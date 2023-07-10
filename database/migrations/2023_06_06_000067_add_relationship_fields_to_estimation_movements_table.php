<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToEstimationMovementsTable extends Migration
{
    public function up()
    {
        Schema::table('estimation_movements', function (Blueprint $table) {
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id', 'product_fk_8534480')->references('id')->on('book_variants');
            $table->unsignedBigInteger('reversal_of_id')->nullable();
            $table->foreign('reversal_of_id', 'reversal_of_fk_8534485')->references('id')->on('estimation_movements');
        });
    }
}
