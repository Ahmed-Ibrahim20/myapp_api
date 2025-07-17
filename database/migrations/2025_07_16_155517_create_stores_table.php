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
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('domain')->unique();
            $table->json('tech_stack')->nullable(); // مثال: ["google-analytics", "hotjar"]
            $table->unsignedBigInteger('user_add_id')->nullable();
            $table->timestamps();
        });
        Schema::table('stores', function (Blueprint $table) {
            $table->foreign('user_add_id')->references('id')->on('users')->onDelete('set null');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
