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
        Schema::create('daily_mental_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('check_date');
            $table->json('answers');
            $table->tinyInteger('total_score');
            $table->enum('category', ['baik', 'perlu_perhatian', 'perlu_pendampingan']);
            $table->boolean('need_help')->default(false);
            $table->text('help_note')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'check_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_mental_checks');
    }
};
