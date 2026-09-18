<?php

use App\Models\City;
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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string('contact_name', 256);
            $table->string('name', 256);
            $table->string('nickname', 256)->nullable();
            $table->string('email');
            $table->string('phone', 20);
            $table->text('address');
            $table->string('neighborhood', 100);
            $table->string('postal_code', 5);
            $table->foreignIdFor(City::class)->constrained();
            $table->foreignIdFor(State::class)->constrained();
            $table->foreignIdFor(Country::class)->constrained();
            $table->nullableMorphs('addressable');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
