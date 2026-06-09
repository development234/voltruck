<?php

namespace App\Http\Controllers;

use App\Models\VolumeMeasurement;
use App\Models\LidarRawData;
use Illuminate\Http\Request;

class MeasurementController extends Controller
{
    public function show($id = null)
    {
        // Jika ID tidak diberikan, ambil pengukuran terbaru
        if (!$id) {
            $measurement = VolumeMeasurement::with('lidarRawData', 'user')
                ->latest('measured_at')
                ->first();
        } else {
            $measurement = VolumeMeasurement::with('lidarRawData', 'user')
                ->findOrFail($id);
        }

        if (!$measurement) {
            //abort(404, 'Belum ada data pengukuran.');
            return view('measurement.empty');
        }

        // Ambil point cloud dari lidar_raw_data jika ada
        $pointCloud = [];
        if ($measurement->lidarRawData && $measurement->lidarRawData->raw_data) {
            $raw = $measurement->lidarRawData->raw_data;
            if (is_string($raw)) {
                $pointCloud = json_decode($raw, true);
            } else {
                $pointCloud = $raw;
            }
        }

        // Data dummy untuk simulasi jika point cloud kosong
        if (empty($pointCloud)) {
            // Buat simulasi titik-titik berbentuk bak truk
            $pointCloud = $this->generateDummyPointCloud();
        }

        // Kirim data ke view
        return view('measurement.show', compact('measurement', 'pointCloud'));
    }

    private function generateDummyPointCloud()
    {
        // Simulasi titik-titik membentuk bak truk (persegi panjang)
        $points = [];
        // Tepi bawah (z=0)
        for ($x = -1.5; $x <= 1.5; $x += 0.1) {
            for ($y = -0.8; $y <= 0.8; $y += 0.1) {
                $points[] = ['x' => $x, 'y' => $y, 'z' => 0];
            }
        }
        // Tumpukan muatan (kerucut di tengah)
        for ($i = 0; $i < 500; $i++) {
            $x = (mt_rand(-120, 120) / 100) * 1.2;
            $y = (mt_rand(-80, 80) / 100) * 0.8;
            $r = sqrt($x*$x + $y*$y);
            $z = max(0, 0.6 * (1 - $r / 1.2)) + mt_rand(-5, 5)/100;
            $points[] = ['x' => $x, 'y' => $y, 'z' => $z];
        }
        return $points;
    }
}
