<?php

use App\Models\Order;
use App\Models\Warehouse;
use App\Models\WarehouseStorageStatus;
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
        Schema::create('warehouse_storage_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('warehouse_storages', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Order::class)->constrained();
            $table->string('reference', 150)->nullable();
            $table->string('comments', 500)->nullable();
            $table->foreignIdFor(Warehouse::class)->nullable()->constrained();
            $table->integer('almacen_id')->nullable();
            $table->decimal('total', 9, 2)->nullable();
            $table->date('payment_date')->nullable();
            $table->foreignIdFor(WarehouseStorageStatus::class)->constrained();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_storage_statuses');
        Schema::dropIfExists('warehouse_storages');
    }
};
