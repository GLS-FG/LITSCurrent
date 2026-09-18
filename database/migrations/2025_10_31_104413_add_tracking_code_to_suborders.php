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
        Schema::table('warehouse_storages', function (Blueprint $table) {
            $table->string('tracking_code', 30)->nullable();
        });

        Schema::table('order_imports', function (Blueprint $table) {
            $table->string('tracking_code', 30)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warehouse_storages', function (Blueprint $table) {
            $table->dropColumn(['tracking_code']);
        });

        Schema::table('order_imports', function (Blueprint $table) {
            $table->dropColumn(['tracking_code']);
        });
    }
};
