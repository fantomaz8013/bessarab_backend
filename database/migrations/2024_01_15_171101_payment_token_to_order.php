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
        Schema::table('orders', function (Blueprint $table) {
            $table->longText('payment_token')->nullable(true);
            $table->string('payment_status')->nullable(true);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->string('short_name')->nullable(true);
            $table->integer('sort')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('payment_token');
            $table->dropColumn('payment_status');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('short_name');
            $table->dropColumn('sort');
        });
    }
};
