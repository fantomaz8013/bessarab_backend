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
        Schema::table('product_sizes', function (Blueprint $table) {
            $table->decimal('price')->nullable(false);
        });

        Schema::table('order_products', function (Blueprint $table) {
            $table->unsignedBigInteger('product_size_id')->nullable(true);
            $table->foreign('product_size_id')->references('id')->on('product_sizes');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_sizes', function (Blueprint $table) {
            $table->dropColumn('price');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->decimal('price')->nullable(true);
        });
    }
};
