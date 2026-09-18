<?php

use App\Models\TransportationAgency;
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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('eco_number', 100);
            $table->string('plates', 100)->nullable();
            $table->string('caat_code', 20)->nullable();
            $table->string('scac_code', 20)->nullable();
            $table->string('vehicle_type', 100)->nullable();
            $table->string('serial_number', 100)->nullable();
            $table->string('origin', 100)->nullable();
            $table->foreignIdFor(TransportationAgency::class)->constrained();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
