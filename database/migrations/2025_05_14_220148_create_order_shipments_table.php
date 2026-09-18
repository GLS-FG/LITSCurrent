<?php

use App\Models\Custom;
use App\Models\Order;
use App\Models\OrderShipmentStatus;
use App\Models\PetitionCode;
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
        Schema::create('order_shipment_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('order_shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Order::class)->constrained();
            $table->foreignIdFor(Custom::class)->nullable()->constrained();
            $table->string('petition', 191)->nullable();
            $table->foreignIdFor(PetitionCode::class)->nullable()->constrained();
            $table->date('petition_date')->nullable();
            $table->string('comments', 1000)->nullable();
            $table->string('shipment_origin', 191)->nullable();
            $table->string('shipment_destination', 191)->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('reference', 150)->nullable();
            $table->date('payment_date')->nullable();
            $table->decimal('total', 9, 2)->nullable();
            $table->string('tracking_code', 30)->nullable();
            $table->string('instructions1', 500)->nullable();
            $table->string('instructions2', 500)->nullable();
            $table->foreignIdFor(OrderShipmentStatus::class)->constrained();
            $table->string('ship_to', 500)->nullable();
            $table->string('ship_from', 500)->nullable();
            $table->string('invoice', 500)->nullable();
            $table->string('currency_code', 3);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_shipments');
        Schema::dropIfExists('order_shipment_statuses');
    }
};
