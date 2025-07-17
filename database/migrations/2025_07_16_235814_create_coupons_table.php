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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->decimal('discount_value', 10, 2);
            $table->tinyInteger('discount_type')->default(0)->comment('0:fixed,1:percentage');
            // $table->boolean('discount_type'); // 0 = نسبة مئوية, 1 = خصم ثابت
            $table->integer('usage_limit');
            $table->date('start_date');
            $table->date('end_date');
            $table->text('description')->nullable();  
            $table->unsignedBigInteger('user_add_id')->nullable();
          
            $table->timestamps();
        });
              Schema::table('coupons', function (Blueprint $table) {
            $table->foreign('user_add_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
