<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEstimationMovementsTable extends Migration
{
    public function up()
    {
        Schema::create('estimation_movements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('movement_date');
            $table->string('movement_type');
            $table->string('reference_type')->nullable();
            $table->string('type');
            $table->integer('quantity')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
