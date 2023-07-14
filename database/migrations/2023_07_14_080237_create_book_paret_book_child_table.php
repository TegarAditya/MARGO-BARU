<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('book_parent_book_child', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id');
            $table->foreign('parent_id')->references('id')->on('book_variants')->onDelete('cascade');
            $table->unsignedBigInteger('child_id');
            $table->foreign('child_id')->references('id')->on('book_variants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_paret_book_child');
    }
};
