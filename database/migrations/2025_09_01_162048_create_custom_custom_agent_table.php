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
        Schema::create('custom_custom_agent', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Custom::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(CustomAgent::class)->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['custom_id', 'custom_agent_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_custom_agent');
    }
};
