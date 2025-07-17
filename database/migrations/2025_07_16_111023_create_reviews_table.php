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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_add_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->tinyInteger('rating')->unsigned(); // 1–5
            $table->text('comment')->nullable();
            $table->timestamps();
        });
              Schema::table('reviews', function (Blueprint $table) {
            $table->foreign('user_add_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
