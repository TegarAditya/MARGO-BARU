<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToReturnGoodsTable extends Migration
{
    public function up()
    {
        Schema::table('return_goods', function (Blueprint $table) {
            $table->unsignedBigInteger('salesperson_id')->nullable();
            $table->foreign('salesperson_id', 'salesperson_fk_8116164')->references('id')->on('salespeople');
            $table->unsignedBigInteger('semester_retur_id')->nullable();
            $table->foreign('semester_retur_id')->references('id')->on('semesters');
            $table->unsignedBigInteger('semester_id')->nullable();
            $table->foreign('semester_id', 'semester_fk_8116165')->references('id')->on('semesters');
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->foreign('created_by_id')->references('id')->on('users');
            $table->unsignedBigInteger('updated_by_id')->nullable();
            $table->foreign('updated_by_id')->references('id')->on('users');
        });
    }
}
