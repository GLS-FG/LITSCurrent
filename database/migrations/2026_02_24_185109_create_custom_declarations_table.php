<?php

use App\Models\Custom;
use App\Models\CustomAgent;
use App\Models\Incoterm;
use App\Models\OrderImport;
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
        Schema::create('custom_declarations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(OrderImport::class)->constrained();
            $table->foreignIdFor(Custom::class)->nullable()->constrained();
            $table->foreignIdFor(CustomAgent::class)->nullable()->constrained();
            $table->string('petition', 191)->nullable();
            $table->date('entry_date')->nullable();
            $table->date('draft_date')->nullable();
            $table->date('paid_date')->nullable();
            $table->decimal('commercial_value', 9, 2)->nullable();
            $table->decimal('customs_value', 9, 2)->nullable();
            $table->decimal('customs_value_foreign', 9, 2)->nullable();
            $table->decimal('exchange_rate', 9, 2)->nullable();
            $table->decimal('gross_weight', 9, 2)->nullable();
            $table->decimal('packages', 9, 2)->nullable();
            $table->string('fiscal_traffic_light')->nullable();
            $table->foreignIdFor(Incoterm::class)->nullable()->constrained();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_declarations');
    }
};
