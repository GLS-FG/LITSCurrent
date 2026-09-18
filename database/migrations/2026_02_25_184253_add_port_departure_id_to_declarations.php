<?php

use App\Models\Custom;
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
        Schema::table('custom_declarations', function (Blueprint $table) {
            $table->foreignIdFor(Custom::class, 'port_departure_id')->nullable()->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('custom_declarations', function (Blueprint $table) {
            //
        });
    }
};
