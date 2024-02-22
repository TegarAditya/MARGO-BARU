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
        Schema::table('transaction_totals', function (Blueprint $table) {
            $table->integer('semester_id')->nullable()->after('salesperson_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction_totals', function (Blueprint $table) {
            $table->dropColumn('semester_id');
        });
    }
};
