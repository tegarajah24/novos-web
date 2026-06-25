<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->index(['user_id', 'product_id', 'size'], 'carts_user_product_size_index');
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->dropUnique('carts_user_id_product_id_size_unique');
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropIndex('carts_user_product_size_index');
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->nullable(false)->change();
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->unique(['user_id', 'product_id', 'size'], 'carts_user_id_product_id_size_unique');
        });
    }
};
