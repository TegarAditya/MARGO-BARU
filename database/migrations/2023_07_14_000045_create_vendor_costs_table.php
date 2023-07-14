<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorCostsTable extends Migration
{
    public function up()
    {
        Schema::create('vendor_costs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('key');
            $table->decimal('value', 15, 2);
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
