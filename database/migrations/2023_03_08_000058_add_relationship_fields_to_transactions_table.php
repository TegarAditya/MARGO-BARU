<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToTransactionsTable extends Migration
{
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('salesperson_id')->nullable();
            $table->foreign('salesperson_id', 'salesperson_fk_8137256')->references('id')->on('salespeople');
            $table->unsignedBigInteger('semester_id')->nullable();
            $table->foreign('semester_id', 'semester_fk_8137257')->references('id')->on('semesters');
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->foreign('reference_id', 'reference_fk_8137259')->references('id')->on('invoices');
        });
    }
}
