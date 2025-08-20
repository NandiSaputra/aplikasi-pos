<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->date('purchase_date');
            $table->integer('total_price')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('purchases');
    }
};


