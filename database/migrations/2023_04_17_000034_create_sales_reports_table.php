<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesReportsTable extends Migration
{
    public function up()
    {
        Schema::create('sales_reports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code');
            $table->string('periode');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('type')->nullable();
            $table->decimal('saldo_awal', 15, 2);
            $table->decimal('debet', 15, 2);
            $table->decimal('kredit', 15, 2);
            $table->decimal('saldo_akhir', 15, 2);
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
