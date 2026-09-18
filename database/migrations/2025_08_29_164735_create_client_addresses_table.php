<?php

use App\Models\City;
use App\Models\Client;
use App\Models\Country;
use App\Models\State;
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
        Schema::create('client_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Client::class)->constrained();
            $table->tinyInteger('address_type')->default(0);
            $table->text('street_name')->nullable();
            $table->string('street_no', 50)->nullable();
            $table->string('neighborhood', 100)->nullable();
            $table->integer('postal_code')->length(5)->unsigned()->nullable();
            $table->foreignIdFor(City::class)->constrained();
            $table->foreignIdFor(State::class)->constrained();
            $table->foreignIdFor(Country::class)->constrained();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_addresses');
    }
};
