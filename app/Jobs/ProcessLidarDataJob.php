<?php
// app/Jobs/ProcessLidarDataJob.php

namespace App\Jobs;

use App\Models\LidarRawData;
use App\Models\VolumeMeasurement;
use App\Services\VolumeCalculatorService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessLidarDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    protected $lidarRawData;

    /**
     * Create a new job instance.
     */
    public function __construct(LidarRawData $lidarRawData)
    {
        $this->lidarRawData = $lidarRawData;
    }

    /**
     * Execute the job.
     */
    public function handle(VolumeCalculatorService $calculator): void
    {
        try {
            // 1. Ambil data mentah (JSON)
            $rawDataJson = $this->lidarRawData->raw_data;
            $pointCloud = json_decode($rawDataJson, true);

            if (!is_array($pointCloud) || empty($pointCloud)) {
                throw new \Exception('Data point cloud tidak valid atau kosong');
            }

            // 2. Proses dengan service calculator
            //    Fungsi-fungsi di VolumeCalculatorService akan mengolah point cloud
            $filteredPoints = $calculator->filterNoise($pointCloud);
            $truckAreaPoints = $calculator->segmentTruckArea($filteredPoints);
            $volume = $calculator->calculateVolume($truckAreaPoints);
            
            // Ekstrak juga panjang, lebar, tinggi rata-rata jika diperlukan
            $dimensions = $calculator->getDimensions($truckAreaPoints);

            // 3. Simpan hasil ke volume_measurements
            VolumeMeasurement::create([
                'user_id'            => $this->lidarRawData->user_id,
                'lidar_raw_data_id'  => $this->lidarRawData->id,
                'volume_m3'          => $volume,
                'length_m'           => null,   // atau hitung manual jika perlu
                'width_m'            => null,
                'height_avg_m'       => null,
                'method'             => 'lidar',
                'measured_at'        => $this->lidarRawData->measured_at,
                'notes'              => 'Diproses otomatis dari LiDAR',
            ]);

            // Opsional: update status atau timestamp di raw data
            $this->lidarRawData->update(['updated_at' => now()]);

            Log::info("Volume berhasil dihitung untuk data ID {$this->lidarRawData->id}: {$volume} m³");

        } catch (\Exception $e) {
            Log::error("Gagal memproses data LiDAR ID {$this->lidarRawData->id}: " . $e->getMessage());
            
            // Simpan error sebagai notes? Bisa juga buat kolom status di raw data
            // Di sini kita biarkan saja, tetapi Anda bisa menambahkan kolom 'processing_error'
            throw $e; // Biarkan job gagal agar bisa di-retry jika perlu
        }
    }
}
