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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('phone')->unique();
            $table->string('alternate_phone')->nullable();
            $table->decimal('total', 10, 2)->default(0);
            $table->string('country')->nullable();
            $table->text('address')->nullable();
            $table->text('note')->nullable();
            $table->unsignedBigInteger('user_add_id')->nullable();
            $table->timestamps();
        });
        Schema::table('suppliers', function (Blueprint $table) {
            $table->foreign('user_add_id')->references('id')->on('users')->onDelete('set null');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
