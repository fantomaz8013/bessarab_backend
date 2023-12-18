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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable(false);
            $table->string('avatar_url')->nullable(false);
            $table->string('description')->nullable(false);
            $table->string('result')->nullable(true);
            $table->string('Purpose')->comment("Назначение продукта")->nullable(true);
            $table->decimal('price')->nullable(true);
            $table->unsignedBigInteger('product_category_id')->nullable(false);
            $table->foreign('product_category_id')->references('id')->on('product_categories');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
