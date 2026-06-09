<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LidarRawData extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'sensor_id', 'raw_data', 'file_path', 'measured_at'
    ];

    protected $casts = [
        'raw_data' => 'array',
        'measured_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function volumeMeasurement()
    {
        return $this->hasOne(VolumeMeasurement::class);
    }
}