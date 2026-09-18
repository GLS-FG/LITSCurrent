<?php

use App\Models\City;
use App\Models\Country;
use App\Models\State;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_shipments', function (Blueprint $table) {
            $table->foreignIdFor(City::class, 'origin_city_id')->nullable()->constrained();
            $table->foreignIdFor(State::class, 'origin_state_id')->nullable()->constrained();
            $table->foreignIdFor(Country::class, 'origin_country_id')->nullable()->constrained();
            $table->foreignIdFor(City::class, 'destination_city_id')->nullable()->constrained();
            $table->foreignIdFor(State::class, 'destination_state_id')->nullable()->constrained();
            $table->foreignIdFor(Country::class, 'destination_country_id')->nullable()->constrained();
        });
    }

    public function down(): void
    {
        Schema::table('order_shipments', function (Blueprint $table) {
            $table->dropForeign([
                'origin_city_id', 'origin_state_id', 'origin_country_id',
                'destination_city_id', 'destination_state_id', 'destination_country_id'
            ]);
            $table->dropIndex([
                'origin_city_id', 'origin_state_id', 'origin_country_id',
                'destination_city_id', 'destination_state_id', 'destination_country_id'
            ]);
            $table->dropColumn([
                'origin_city_id', 'origin_state_id', 'origin_country_id',
                'destination_city_id', 'destination_state_id', 'destination_country_id'
            ]);
        });
    }
};
