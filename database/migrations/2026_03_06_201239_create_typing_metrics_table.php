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
        Schema::create('typing_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jawaban_id')->constrained('jawaban')->cascadeOnDelete();
            $table->integer('keystroke_count')->default(0);
            $table->integer('char_count_final')->default(0);
            $table->decimal('keystroke_ratio', 5, 3)->default(0);
            $table->timestamp('typing_started_at')->nullable();
            $table->integer('char_count_per_minute')->default(0);
            $table->integer('auto_clear_count')->default(0);
            $table->boolean('is_suspicious')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('typing_metrics');
    }
};
