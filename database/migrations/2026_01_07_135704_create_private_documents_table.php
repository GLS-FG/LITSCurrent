<?php

use App\Models\PrivateDocumentType;
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
        Schema::create('private_documents', function (Blueprint $table) {
            $table->id();
            $table->string('name', 256);
            $table->string('original_name', 256);
            $table->integer('size_bytes');
            $table->string('size_label');
            $table->string('mime_type');
            $table->foreignIdFor(PrivateDocumentType::class)->constrained();
            $table->morphs('documentable');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('private_documents');
    }
};
