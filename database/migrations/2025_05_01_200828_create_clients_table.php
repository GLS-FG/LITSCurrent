<?php

use App\Models\User;
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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained();
            $table->string('name', 100);
            $table->string('last_name', 100);
            $table->string('company_name', 200);
            $table->text('trade_name');
            $table->string('federal_tax_id', 32)->nullable();
            $table->string('national_id', 32)->nullable();
            $table->string('email');
            $table->string('phone1', 20);
            $table->string('phone2', 20)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
