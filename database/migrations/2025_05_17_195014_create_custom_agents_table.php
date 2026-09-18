<?php

use App\Models\Custom;
use App\Models\CustomAgent;
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
        Schema::create('custom_agents', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('last_name', 100);
            $table->string('patent', 10);
            $table->string('phone1', 20);
            $table->string('phone2', 20);
            $table->foreignIdFor(Custom::class)->constrained();
            $table->timestamps();
        });

        Schema::table('order_imports', function (Blueprint $table) {
            $table->foreignIdFor(CustomAgent::class)->nullable()->constrained();
        });

        Schema::table('order_exports', function (Blueprint $table) {
            $table->foreignIdFor(CustomAgent::class)->nullable()->constrained();
        });

        Schema::table('order_shipments', function (Blueprint $table) {
            $table->foreignIdFor(CustomAgent::class)->nullable()->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_agents');
    }
};
