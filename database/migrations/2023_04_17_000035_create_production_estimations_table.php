<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductionEstimationsTable extends Migration
{
    public function up()
    {
        Schema::create('production_estimations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type');
            $table->integer('estimasi')->nullable()->default(0);
            $table->integer('sales')->nullable()->default(0);
            $table->integer('internal')->nullable()->default(0);
            $table->integer('produksi')->nullable()->default(0);
            $table->integer('realisasi')->nullable()->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
