<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('volume_measurements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('lidar_raw_data_id')->nullable()
                  ->constrained('lidar_raw_data')
                  ->onDelete('set null');
            $table->decimal('volume_m3', 10, 2);
            $table->decimal('length_m', 8, 2)->nullable();
            $table->decimal('width_m', 8, 2)->nullable();
            $table->decimal('height_avg_m', 8, 2)->nullable();
            $table->string('method');
            $table->text('notes')->nullable();
            $table->timestamp('measured_at');
            $table->timestamps();

            $table->index('user_id');
            $table->index('measured_at');
            $table->index('method');
        });
    }

    public function down()
    {
        Schema::dropIfExists('volume_measurements');
    }
};