<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToPlatePrintsTable extends Migration
{
    public function up()
    {
        Schema::table('plate_prints', function (Blueprint $table) {
            $table->unsignedBigInteger('semester_id')->nullable();
            $table->foreign('semester_id', 'semester_fk_8797599')->references('id')->on('semesters');
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->foreign('vendor_id', 'vendor_fk_8797600')->references('id')->on('vendors');
        });
    }
}
