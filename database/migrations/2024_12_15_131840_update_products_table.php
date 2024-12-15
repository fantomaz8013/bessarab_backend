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
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('brand_id')->nullable(false)->default(1);
            $table->foreign('brand_id')->references('id')->on('brands');
        });
        Schema::table('product_lines', function (Blueprint $table) {
            $table->unsignedBigInteger('brand_id')->nullable(false)->default(1);
            $table->foreign('brand_id')->references('id')->on('brands');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('brand_id');
        });
        Schema::table('product_lines', function (Blueprint $table) {
            $table->dropColumn('brand_id');
        });
    }
};
