<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('invoice_number')->unique();
            $table->integer('total_price');
            $table->integer('paid_amount');
            $table->integer('change_amount');
            $table->string('payment_method')->nullable();
            $table->string('payment_status')->default('pending');
            $table->string('snap_token')->nullable();
            $table->string('coupon_code')->nullable();
            $table->integer('discount_amount')->default(0);
            $table->integer('product_discount_total')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('transactions');
    }
};
