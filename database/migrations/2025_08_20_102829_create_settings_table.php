<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('store_name')->default('Nama Toko');
            $table->string('store_address')->nullable();
            $table->string('store_phone')->nullable();
            $table->string('store_logo')->nullable();
            $table->enum('payment_methods', ['cash'])->default('cash'); // kalau nanti tambah bisa array/json
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
