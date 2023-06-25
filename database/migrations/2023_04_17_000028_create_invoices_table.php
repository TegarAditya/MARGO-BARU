<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('no_faktur')->unique();
            $table->date('date');
            $table->decimal('total', 15, 2);
            $table->decimal('discount', 15, 2)->nullable();
            $table->decimal('nominal', 15, 2);
            $table->longText('note')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
