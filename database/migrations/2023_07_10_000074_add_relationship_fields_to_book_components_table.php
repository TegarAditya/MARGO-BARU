<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToBookComponentsTable extends Migration
{
    public function up()
    {
        Schema::table('book_components', function (Blueprint $table) {
            $table->unsignedBigInteger('jenjang_id')->nullable();
            $table->foreign('jenjang_id', 'jenjang_fk_8736059')->references('id')->on('jenjangs');
            $table->unsignedBigInteger('kurikulum_id')->nullable();
            $table->foreign('kurikulum_id', 'kurikulum_fk_8736060')->references('id')->on('kurikulums');
            $table->unsignedBigInteger('isi_id')->nullable();
            $table->foreign('isi_id', 'isi_fk_8736061')->references('id')->on('isis');
            $table->unsignedBigInteger('cover_id')->nullable();
            $table->foreign('cover_id', 'cover_fk_8736062')->references('id')->on('covers');
            $table->unsignedBigInteger('mapel_id')->nullable();
            $table->foreign('mapel_id', 'mapel_fk_8736063')->references('id')->on('mapels');
            $table->unsignedBigInteger('kelas_id')->nullable();
            $table->foreign('kelas_id', 'kelas_fk_8736064')->references('id')->on('kelas');
            $table->unsignedBigInteger('halaman_id')->nullable();
            $table->foreign('halaman_id', 'halaman_fk_8736065')->references('id')->on('halamen');
            $table->unsignedBigInteger('semester_id')->nullable();
            $table->foreign('semester_id', 'semester_fk_8736066')->references('id')->on('semesters');
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->foreign('warehouse_id', 'warehouse_fk_8736067')->references('id')->on('warehouses');
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->foreign('unit_id', 'unit_fk_8736069')->references('id')->on('units');
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->foreign('created_by_id', 'created_by_fk_8736073')->references('id')->on('users');
            $table->unsignedBigInteger('updated_by_id')->nullable();
            $table->foreign('updated_by_id', 'updated_by_fk_8736074')->references('id')->on('users');
        });
    }
}
