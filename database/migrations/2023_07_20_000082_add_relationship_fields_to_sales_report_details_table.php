<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToSalesReportDetailsTable extends Migration
{
    public function up()
    {
        Schema::table('sales_report_details', function (Blueprint $table) {
            $table->unsignedBigInteger('sales_report_id')->nullable();
            $table->foreign('sales_report_id', 'sales_report_fk_8771888')->references('id')->on('sales_reports');
        });
    }
}
