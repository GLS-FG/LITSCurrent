<?php

use App\Models\ServiceClass;
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
        Schema::create('service_modes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10);
            $table->string('name', 256);
            $table->foreignIdFor(ServiceClass::class)->constrained();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_modes');
    }
};
