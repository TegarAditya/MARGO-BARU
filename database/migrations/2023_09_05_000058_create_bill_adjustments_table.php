<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillAdjustmentsTable extends Migration
{
    public function up()
    {
        Schema::create('bill_adjustments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('no_adjustment')->unique();
            $table->date('date');
            $table->decimal('amount', 15, 2);
            $table->longText('note')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
