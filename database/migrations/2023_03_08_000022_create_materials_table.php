<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaterialsTable extends Migration
{
    public function up()
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code')->unique();
            $table->string('name');
            $table->longText('description')->nullable();
            $table->string('category');
            $table->decimal('cost', 15, 2);
            $table->float('stock', 15, 2);
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
