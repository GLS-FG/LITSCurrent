<?php

use App\Models\Order;
use App\Models\Warehouse;
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
        Schema::create('order_products', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Order::class)->constrained();
            $table->string('product', 191);
            $table->string('dimensions', 191)->nullable();
            $table->string('weight', 191)->nullable();
            $table->decimal('quantity', 9, 2)->nullable();
            $table->string('container', 191)->nullable();
            $table->decimal('value', 9, 2)->nullable();
            $table->string('insured', 5)->nullable();
            $table->foreignIdFor(Warehouse::class)->nullable()->constrained();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_products');
    }
};
