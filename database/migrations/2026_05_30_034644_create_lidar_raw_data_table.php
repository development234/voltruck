<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lidar_raw_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // siapa yang melakukan pengukuran
            $table->string('sensor_id'); // ID sensor LiDAR
            $table->json('raw_data'); // data mentah dari sensor (point cloud atau string)
            $table->string('file_path')->nullable(); // jika data disimpan sebagai file
            $table->timestamp('measured_at'); // waktu pengukuran
            $table->timestamps();

            $table->index('user_id');
            $table->index('measured_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('lidar_raw_data');
    }
};