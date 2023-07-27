<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlatePrintItemsTable extends Migration
{
    public function up()
    {
        Schema::create('plate_print_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('plate_qty');
            $table->float('chemical_qty', 15, 2);
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
