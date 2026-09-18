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
        Schema::table('order_products', function (Blueprint $table) {
            $table->string('reference')->nullable();
            $table->decimal('height', 9, 2)->nullable();
            $table->decimal('width', 9, 2)->nullable();
            $table->decimal('length', 9, 2)->nullable();
            $table->string('unit_measure')->nullable();
            $table->string('haz_mat', 5)->nullable();
            $table->string('incoterm')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_products', function (Blueprint $table) {
            $table->dropColumn(['reference', 'height', 'width', 'length', 'unit_measure', 'haz_mat', 'incoterm']);
        });
    }
};
