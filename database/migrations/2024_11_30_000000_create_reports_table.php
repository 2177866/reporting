<?php

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
        Schema::create('reports', function (Blueprint $table) {

            // $table->id('id');
            $table->uuid('id')->primary();

            // $table->morphs('reportable');
            $table->uuidMorphs('reportable');

            // $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignUuid('user_id')->nullable()->constrained()->cascadeOnDelete();

            $table->string('reason'); // Причина жалобы
            $table->json('meta')->nullable(); // Дополнительные метаданные
            $table->softDeletes(); // Поддержка soft delete
            $table->timestamps(); // created_at и updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
