<?php

use App\Models\ServiceClass;
use App\Models\ServiceLevel;
use App\Models\ServiceMode;
use App\Models\ClassType;
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
        Schema::table('order_shipments', function (Blueprint $table) {
            $table->foreignIdFor(ServiceClass::class)->nullable()->constrained();
            $table->foreignIdFor(ServiceMode::class)->nullable()->constrained();
            $table->foreignIdFor(ClassType::class)->nullable()->constrained();
            $table->foreignIdFor(ServiceLevel::class)->nullable()->constrained();
            $table->string('oversize', 2)->default('No');
            $table->string('hazardous_material', 2)->default('No');
            $table->string('refrigerated', 2)->default('No');
            $table->string('insurance', 2)->default('No');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_shipments', function (Blueprint $table) {
            $table->dropForeign(['service_class_id', 'service_mode_id', 'class_type_id', 'service_level_id']);
            $table->dropColumn(['service_class_id', 'service_mode_id', 'class_type_id', 'service_level_id']);
            $table->dropColumn(['oversize', 'hazardous_material', 'refrigerated', 'insurance']);
        });
    }
};
