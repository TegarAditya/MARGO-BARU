<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToBooksTable extends Migration
{
    public function up()
    {
        Schema::table('books', function (Blueprint $table) {
            $table->unsignedBigInteger('jenjang_id')->nullable();
            $table->foreign('jenjang_id', 'jenjang_fk_8102557')->references('id')->on('jenjangs');
            $table->unsignedBigInteger('kurikulum_id')->nullable();
            $table->foreign('kurikulum_id', 'kurikulum_fk_8102558')->references('id')->on('kurikulums');
            $table->unsignedBigInteger('mapel_id')->nullable();
            $table->foreign('mapel_id', 'mapel_fk_8102559')->references('id')->on('mapels');
            $table->unsignedBigInteger('kelas_id')->nullable();
            $table->foreign('kelas_id', 'kelas_fk_8102560')->references('id')->on('kelas');
            $table->unsignedBigInteger('cover_id')->nullable();
            $table->foreign('cover_id', 'cover_fk_8102561')->references('id')->on('covers');
            $table->unsignedBigInteger('semester_id')->nullable();
            $table->foreign('semester_id', 'semester_fk_8102562')->references('id')->on('semesters');
        });
    }
}
