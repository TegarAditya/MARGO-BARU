<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToBillAdjustmentsTable extends Migration
{
    public function up()
    {
        Schema::table('bill_adjustments', function (Blueprint $table) {
            $table->unsignedBigInteger('salesperson_id')->nullable();
            $table->foreign('salesperson_id', 'salesperson_fk_8959695')->references('id')->on('salespeople');
            $table->unsignedBigInteger('semester_id')->nullable();
            $table->foreign('semester_id', 'semester_fk_8959696')->references('id')->on('semesters');
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->foreign('created_by_id', 'created_by_fk_8959699')->references('id')->on('users');
            $table->unsignedBigInteger('updated_by_id')->nullable();
            $table->foreign('updated_by_id', 'updated_by_fk_8959700')->references('id')->on('users');
        });
    }
}
