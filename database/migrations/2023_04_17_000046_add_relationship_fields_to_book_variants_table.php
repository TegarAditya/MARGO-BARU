<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToBookVariantsTable extends Migration
{
    public function up()
    {
        Schema::table('book_variants', function (Blueprint $table) {
            $table->unsignedBigInteger('book_id')->nullable();
            $table->foreign('book_id', 'book_fk_8102567')->references('id')->on('books');
            $table->unsignedBigInteger('jenjang_id')->nullable();
            $table->foreign('jenjang_id', 'jenjang_fk_8106298')->references('id')->on('jenjangs');
            $table->unsignedBigInteger('kurikulum_id')->nullable();
            $table->foreign('kurikulum_id', 'kurikulum_fk_8106300')->references('id')->on('kurikulums');
            $table->unsignedBigInteger('isi_id')->nullable();
            $table->foreign('isi_id')->references('id')->on('isis');
            $table->unsignedBigInteger('cover_id')->nullable();
            $table->foreign('cover_id', 'cover_fk_8669583')->references('id')->on('covers');
            $table->unsignedBigInteger('mapel_id')->nullable();
            $table->foreign('mapel_id', 'mapel_fk_8669584')->references('id')->on('mapels');
            $table->unsignedBigInteger('kelas_id')->nullable();
            $table->foreign('kelas_id', 'kelas_fk_8669585')->references('id')->on('kelas');
            $table->unsignedBigInteger('halaman_id')->nullable();
            $table->foreign('halaman_id', 'halaman_fk_8102570')->references('id')->on('halamen');
            $table->unsignedBigInteger('semester_id')->nullable();
            $table->foreign('semester_id', 'semester_fk_8106299')->references('id')->on('semesters');
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->foreign('warehouse_id', 'warehouse_fk_8104173')->references('id')->on('warehouses');
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->foreign('unit_id', 'unit_fk_8104174')->references('id')->on('units');
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->foreign('created_by_id')->references('id')->on('users');
            $table->unsignedBigInteger('updated_by_id')->nullable();
            $table->foreign('updated_by_id')->references('id')->on('users');
        });
    }
}
