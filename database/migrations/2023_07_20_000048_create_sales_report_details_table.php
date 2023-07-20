<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesReportDetailsTable extends Migration
{
    public function up()
    {
        Schema::create('sales_report_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type');
            $table->decimal('amount', 15, 2);
            $table->decimal('debet', 15, 2)->nullable();
            $table->decimal('kredit', 15, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
