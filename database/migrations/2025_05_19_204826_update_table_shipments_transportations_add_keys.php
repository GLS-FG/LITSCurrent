<?php

use App\Models\Incoterm;
use App\Models\Transportation;
use App\Models\TransportationStatus;
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
            $table->foreignIdFor(TransportationStatus::class)->constrained();
            $table->foreignIdFor(Incoterm::class)->constrained();
        });

        Schema::table('order_shipments', function (Blueprint $table) {
            $table->foreignIdFor(Transportation::class)->nullable()->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
