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
        Schema::table('geolocations', function (Blueprint $table) {
            $table->unsignedBigInteger('transportation_id')->nullable()->change();
            $table->string('comments', 500)->nullable();
        });

        Schema::table('shipment_locations', function (Blueprint $table) {
            $table->dropColumn(['tracking_type', 'ended']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('geolocations', function (Blueprint $table) {
            //
        });
    }
};
