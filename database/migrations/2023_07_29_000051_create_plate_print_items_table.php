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
            $table->string('product_text')->nullable();
            $table->integer('estimasi')->nullable();
            $table->integer('realisasi')->nullable();
            $table->longText('note')->nullable();
            $table->string('status')->nullable();
            $table->boolean('check_mapel')->default(0)->nullable();
            $table->boolean('check_kelas')->default(0)->nullable();
            $table->boolean('check_kurikulum')->default(0)->nullable();
            $table->boolean('check_kolomnama')->default(0)->nullable();
            $table->boolean('check_naskah')->default(0)->nullable();
            $table->boolean('surat_jalan')->default(0)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
