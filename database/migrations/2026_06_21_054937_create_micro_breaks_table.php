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
        Schema::create('micro_breaks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('check_date');
            $table->json('checklist');
            $table->tinyInteger('score');
            $table->enum('level', ['tinggi', 'sedang', 'rendah']);
            $table->json('eval');
            $table->text('catatan_membantu')->nullable();
            $table->text('catatan_kendala')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'check_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('micro_breaks');
    }
};
