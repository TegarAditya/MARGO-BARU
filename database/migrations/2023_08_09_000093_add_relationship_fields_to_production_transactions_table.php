<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToProductionTransactionsTable extends Migration
{
    public function up()
    {
        Schema::table('production_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->foreign('vendor_id', 'vendor_fk_8854849')->references('id')->on('vendors');
            $table->unsignedBigInteger('semester_id')->nullable();
            $table->foreign('semester_id', 'semester_fk_8854850')->references('id')->on('semesters');
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->unsignedBigInteger('reversal_of_id')->nullable();
            $table->foreign('reversal_of_id', 'reversal_of_fk_8854858')->references('id')->on('transactions');
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->foreign('created_by_id', 'created_by_fk_8854859')->references('id')->on('users');
            $table->unsignedBigInteger('updated_by_id')->nullable();
            $table->foreign('updated_by_id', 'updated_by_fk_8854860')->references('id')->on('users');
        });
    }
}
