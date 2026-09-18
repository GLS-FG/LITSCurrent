<?php

use App\Models\Geolocation;
use App\Models\GeolocationStatus;
use App\Models\OrderShipment;
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
        Schema::create('shipment_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Geolocation::class)->constrained();
            $table->foreignIdFor(GeolocationStatus::class)->constrained();
            $table->foreignIdFor(OrderShipment::class)->constrained();
            $table->integer('tracking_type');
            $table->boolean('ended');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_locations');
    }
};
