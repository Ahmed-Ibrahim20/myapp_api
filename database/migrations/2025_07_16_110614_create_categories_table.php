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
        
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('note')->nullable(); // ملاحظة غير مطلوبة
            $table->string('image')->nullable(); // مسار أو اسم الصورة
            $table->unsignedBigInteger('user_add_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->timestamps(); // created_at و updated_at
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->foreign('user_add_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
