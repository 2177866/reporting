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

            // $table->uuid('id')->primary();
            // $table->uuidMorphs('reportable');
            // $table->uuid('user_id')->nullable()->index();

            $table->id();
            $table->morphs('reportable');
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();

            $table->string('reason'); // Причина жалобы
            $table->json('meta')->nullable(); // Дополнительные метаданные
            $table->softDeletes(); // Поддержка soft delete
            $table->timestamps(); // created_at и updated_at

            // Получение SQL через DB::getQueryLog()
            // DB::listen(function ($query) {
            //     dump($query->sql);
            // });
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
