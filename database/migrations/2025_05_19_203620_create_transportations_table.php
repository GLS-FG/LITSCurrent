<?php

use App\Models\TransportationAgency;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\City;
use App\Models\Country;
use App\Models\State;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transportations', function (Blueprint $table) {
            $table->id();
            $table->string('transportation_type', 191);
            $table->string('plates', 191);
            $table->foreignIdFor(City::class, 'origin_city_id')->constrained();
            $table->foreignIdFor(State::class, 'origin_state_id')->constrained();
            $table->foreignIdFor(Country::class, 'origin_country_id')->constrained();
            $table->foreignIdFor(City::class, 'destination_city_id')->constrained();
            $table->foreignIdFor(State::class, 'destination_state_id')->constrained();
            $table->foreignIdFor(Country::class, 'destination_country_id')->constrained();
            $table->foreignIdFor(TransportationAgency::class)->constrained();
            $table->string('driver', 191);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transportations');
    }
};
