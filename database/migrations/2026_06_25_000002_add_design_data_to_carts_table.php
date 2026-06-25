<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->json('design_data')->nullable()->after('is_selected');
            $table->text('notes')->nullable()->after('design_data');
            $table->string('image')->nullable()->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn(['design_data', 'notes', 'image']);
        });
    }
};
