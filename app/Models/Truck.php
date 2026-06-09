<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Truck extends Model
{
    use HasFactory;

    protected $fillable = [
        'plate_number', 'name', 'model', 'length_m', 'width_m', 'height_m', 'capacity_m3'
    ];

    public function volumeMeasurements()
    {
        return $this->hasMany(VolumeMeasurement::class);
    }
}