<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->index('status');
            $table->index('created_at');
            $table->index('updated_at');
            $table->index(['status', 'created_at']);
        });

        Schema::table('order_status_histories', function (Blueprint $table) {
            $table->index('status');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['status', 'created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['status']);
        });

        Schema::table('order_status_histories', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['status']);
        });
    }
};
