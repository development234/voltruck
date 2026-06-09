<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VolumeMeasurement extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'lidar_raw_data_id', 'volume_m3', 'length_m', 'width_m',
        'height_avg_m', 'method', 'notes', 'measured_at'
    ];

    protected $casts = [
        'measured_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lidarRawData()
    {
        return $this->belongsTo(LidarRawData::class);
    }
}