<?php

use App\Models\ClassType;
use App\Models\Incoterm;
use App\Models\ServiceClass;
use App\Models\ServiceLevel;
use App\Models\ServiceMode;
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
        Schema::table('order_imports', function (Blueprint $table) {
            $table->foreignIdFor(ServiceClass::class)->nullable()->constrained();
            $table->foreignIdFor(ServiceMode::class)->nullable()->constrained();
            $table->foreignIdFor(ClassType::class)->nullable()->constrained();
            $table->foreignIdFor(ServiceLevel::class)->nullable()->constrained();
            $table->date('entry_date')->nullable();
            $table->decimal('incremental', 9, 2)->nullable();
            $table->decimal('customs_value', 9, 2)->nullable();
            $table->decimal('exchange_rate', 9, 2)->nullable();
            $table->decimal('gross_weight', 9, 2)->nullable();
            $table->decimal('packages', 9, 2)->nullable();
            $table->string('fiscal_traffic_light')->nullable();
            $table->foreignIdFor(Incoterm::class)->nullable()->constrained();
            $table->string('currency_code')->default('MX')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_imports', function (Blueprint $table) {
            //
        });
    }
};
