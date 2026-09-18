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
        Schema::table('transportations', function (Blueprint $table) {
            $table->dropForeign(['origin_city_id']);
            $table->dropForeign(['origin_state_id']);
            $table->dropForeign(['origin_country_id']);
            $table->dropForeign(['destination_city_id']);
            $table->dropForeign(['destination_state_id']);
            $table->dropForeign(['destination_country_id']);
            $table->dropColumn(['origin_city_id', 'origin_state_id', 'origin_country_id', 'destination_city_id', 'destination_state_id', 'destination_country_id']);
            $table->dropColumn(['start_date', 'end_date', 'ship_to', 'ship_from']);
            $table->string('unit_eco_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transportations', function (Blueprint $table) {
            //
        });
    }
};
