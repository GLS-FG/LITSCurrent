<?php

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
        Schema::table('transportations', function (Blueprint $table) {
            $table->foreignIdFor(OrderShipment::class)->nullable()->constrained();
            $table->string('ship_to', 500)->nullable();
            $table->string('ship_from', 500)->nullable();
            $table->string('tracking_link', 500)->nullable();
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
