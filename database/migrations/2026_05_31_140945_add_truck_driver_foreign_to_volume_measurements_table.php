<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('volume_measurements', function (Blueprint $table) {
            $table->foreignId('truck_id')->nullable()->constrained('trucks')->onDelete('set null');
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('volume_measurements', function (Blueprint $table) {
            $table->dropForeign(['truck_id']);
            $table->dropForeign(['driver_id']);
            $table->dropColumn(['truck_id', 'driver_id']);
        });
    }
};