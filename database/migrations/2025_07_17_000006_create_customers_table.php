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
        Schema::create('customers', function (Blueprint $table) {
           $table->id();
            $table->string('full_name');
            $table->string('phone')->unique();
            $table->string('alternate_phone')->nullable();
            $table->string('country');
            $table->text('address');
            $table->text('note')->nullable();
            $table->unsignedBigInteger('user_add_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps(); 
        });
         Schema::table('customers', function (Blueprint $table) {
            $table->foreign('user_add_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
