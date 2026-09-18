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
        Schema::table('order_imports', function (Blueprint $table) {
            $table->boolean('urgent')->default(false);
        });
        Schema::table('warehouse_storages', function (Blueprint $table) {
            $table->boolean('urgent')->default(false);
        });
        Schema::table('vehicles', function (Blueprint $table) {
            $table->string('vehicle_brand')->nullable();
            $table->string('vehicle_model')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_imports', function (Blueprint $table) {
            //
        });
    }
};
