<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockSaldosTable extends Migration
{
    public function up()
    {
        Schema::create('stock_saldos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code')->nullable();
            $table->string('periode');
            $table->date('start_date');
            $table->date('end_date');
            $table->float('qty_awal', 15, 2);
            $table->float('in', 15, 2);
            $table->float('out', 15, 2);
            $table->float('qty_akhir', 15, 2);
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
