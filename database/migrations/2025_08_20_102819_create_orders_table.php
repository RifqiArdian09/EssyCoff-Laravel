<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('no_order')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // kasir yg input
            $table->string('customer_name')->nullable();
            $table->decimal('total', 12, 2);
            $table->decimal('uang_dibayar', 12, 2)->nullable();
            $table->decimal('kembalian', 12, 2)->nullable();
            $table->enum('status', ['pending_payment', 'paid'])->default('pending_payment');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
