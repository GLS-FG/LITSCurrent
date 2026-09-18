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
        Schema::table('custom_declarations', function (Blueprint $table) {
            $table->decimal('commercial_value', 15, 2)->nullable()->change();
            $table->decimal('customs_value', 15, 2)->nullable()->change();
            $table->decimal('customs_value_foreign', 15, 2)->nullable()->change();
            $table->decimal('exchange_rate', 15, 2)->nullable()->change();
            $table->decimal('gross_weight', 15, 2)->nullable()->change();
            $table->decimal('packages', 15, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('custom_declarations', function (Blueprint $table) {
            //
        });
    }
};
