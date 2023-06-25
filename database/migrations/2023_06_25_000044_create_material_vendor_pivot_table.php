<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaterialVendorPivotTable extends Migration
{
    public function up()
    {
        Schema::create('material_vendor', function (Blueprint $table) {
            $table->unsignedBigInteger('material_id');
            $table->foreign('material_id', 'material_id_fk_8669499')->references('id')->on('materials')->onDelete('cascade');
            $table->unsignedBigInteger('vendor_id');
            $table->foreign('vendor_id', 'vendor_id_fk_8669499')->references('id')->on('vendors')->onDelete('cascade');
        });
    }
}
