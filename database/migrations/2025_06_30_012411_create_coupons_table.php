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
            $table->string('code')->unique(); // Kode unik kupon (ex: DISKON10)
            $table->enum('type', ['percentage', 'fixed']); // Tipe diskon
            $table->integer('value'); // Nilai diskon (misal 10% atau 5000)
            $table->integer('min_purchase')->nullable(); // Minimal pembelian
            $table->integer('usage_limit')->nullable(); // Max pemakaian
            $table->integer('used_count')->default(0); // Sudah dipakai berapa kali
            $table->date('expired_at')->nullable(); // Kadaluarsa
            $table->timestamps();
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
