<?php

use App\Models\ClassType;
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
        Schema::table('warehouse_storages', function (Blueprint $table) {
            $table->foreignIdFor(ServiceClass::class)->nullable()->constrained();
            $table->foreignIdFor(ServiceMode::class)->nullable()->constrained();
            $table->foreignIdFor(ClassType::class)->nullable()->constrained();
            $table->foreignIdFor(ServiceLevel::class)->nullable()->constrained();
            $table->string('receipt')->nullable();
            $table->date('receipt_date')->nullable();
            $table->string('document')->nullable();
            $table->date('document_date')->nullable();
            $table->string('currency_code')->default('MX')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warehouse_storages', function (Blueprint $table) {
            //
        });
    }
};
