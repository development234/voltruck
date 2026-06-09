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
        Schema::create('trucks', function (Blueprint $table) {
            $table->id();
            $table->string('plate_number')->unique(); // plat nomor
            $table->string('name')->nullable(); // nama truk (misal: "Truk Pasir")
            $table->string('model')->nullable(); // model
            $table->decimal('length_m', 5, 2); // panjang bak
            $table->decimal('width_m', 5, 2);  // lebar bak
            $table->decimal('height_m', 5, 2); // tinggi bak
            $table->decimal('capacity_m3', 8, 2); // kapasitas maksimal (opsional)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trucks');
    }
};
