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
        Schema::table('design_files', function (Blueprint $table) {
            $table->string('filename', 255)->after('type');
            $table->string('path', 1024)->nullable()->after('filename');
            $table->integer('size')->nullable()->after('path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('design_files', function (Blueprint $table) {
            $table->dropColumn('filename');
            $table->dropColumn('path');
            $table->dropColumn('size');
        });
    }
};
