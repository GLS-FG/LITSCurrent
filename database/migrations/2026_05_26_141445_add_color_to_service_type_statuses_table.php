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
        Schema::table('service_type_statuses', function (Blueprint $table) {
            $table->string('color')->default("bg-gray-50 text-gray-600 inset-ring-gray-500/10");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_type_statuses', function (Blueprint $table) {
            //
        });
    }
};
