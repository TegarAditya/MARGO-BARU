<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillsTable extends Migration
{
    public function up()
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('saldo_awal', 15, 2);
            $table->decimal('jual', 15, 2)->default(0);
            $table->decimal('diskon', 15, 2)->default(0);
            $table->decimal('retur', 15, 2)->default(0);
            $table->decimal('bayar', 15, 2)->default(0);
            $table->decimal('potongan', 15, 2)->default(0);
            $table->decimal('saldo_akhir', 15, 2);
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
