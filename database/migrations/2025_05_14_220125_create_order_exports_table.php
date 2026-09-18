<?php

use App\Models\Custom;
use App\Models\Order;
use App\Models\OrderExportStatus;
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
        Schema::create('order_export_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('order_exports', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Order::class)->constrained();
            $table->string('petition', 191)->nullable();
            $table->foreignIdFor(PetitionCode::class)->nullable()->constrained();
            $table->date('petition_date')->nullable();
            $table->string('comments', 500)->nullable();
            $table->foreignIdFor(Custom::class)->nullable()->constrained();
            $table->string('reference', 150)->nullable();
            $table->date('payment_date')->nullable();
            $table->decimal('total', 9, 2)->nullable();
            $table->foreignIdFor(OrderExportStatus::class)->constrained();
            $table->string('currency_code', 3);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_exports');
        Schema::dropIfExists('order_export_statuses');
    }
};
