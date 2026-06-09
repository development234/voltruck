<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class VolumeCalculatorService
{
    /**
     * Parse raw data from LiDAR (string/JSON) into point cloud array.
     */
    public function parseRawData($rawData): array
    {
        // Asumsikan rawData adalah JSON dengan format: [{"x":0.1,"y":0.2,"z":0.3}, ...]
        if (is_string($rawData)) {
            $decoded = json_decode($rawData, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
            // Jika bukan JSON, coba parse teks CSV atau format lain
            Log::warning("Raw data not JSON, attempting text parse");
            return $this->parseTextFormat($rawData);
        }
        return $rawData ?? [];
    }

    /**
     * Contoh parsing format teks: "x,y,z;x,y,z;..."
     */
    protected function parseTextFormat(string $text): array
    {
        $points = [];
        $lines = explode(';', $text);
        foreach ($lines as $line) {
            $parts = explode(',', $line);
            if (count($parts) >= 3) {
                $points[] = [
                    'x' => (float)$parts[0],
                    'y' => (float)$parts[1],
                    'z' => (float)$parts[2],
                ];
            }
        }
        return $points;
    }

    /**
     * Filter noise dari point cloud (sederhana: hilangkan outlier).
     */
    public function filterNoise(array $points, float $zMin = -2, float $zMax = 5): array
    {
        return array_filter($points, function ($p) use ($zMin, $zMax) {
            return isset($p['z']) && $p['z'] >= $zMin && $p['z'] <= $zMax;
        });
    }

    /**
     * Segmentasi area bak truk: deteksi area dengan intensitas tinggi atau berdasarkan bounding box.
     * Di sini kita asumsikan area bak adalah area dengan x dan y dalam rentang tertentu.
     */
    public function segmentTruckArea(array $points, float $xMin = -1, float $xMax = 1, float $yMin = -0.5, float $yMax = 0.5): array
    {
        return array_filter($points, function ($p) use ($xMin, $xMax, $yMin, $yMax) {
            return $p['x'] >= $xMin && $p['x'] <= $xMax && $p['y'] >= $yMin && $p['y'] <= $yMax;
        });
    }

    /**
     * Hitung volume dari point cloud (metode: integral tinggi × luas grid).
     */
    public function calculateVolume(array $points, float $gridSize = 0.05): float
    {
        if (empty($points)) {
            return 0.0;
        }

        // Dapatkan batas x dan y
        $xs = array_column($points, 'x');
        $ys = array_column($points, 'y');
        if (empty($xs) || empty($ys)) return 0.0;

        $xMin = min($xs);
        $xMax = max($xs);
        $yMin = min($ys);
        $yMax = max($ys);

        // Buat grid
        $xGrids = ceil(($xMax - $xMin) / $gridSize);
        $yGrids = ceil(($yMax - $yMin) / $gridSize);

        $totalVolume = 0.0;
        $cellArea = $gridSize * $gridSize;

        // Untuk setiap sel grid, cari rata-rata z
        for ($i = 0; $i < $xGrids; $i++) {
            $xCellMin = $xMin + $i * $gridSize;
            $xCellMax = $xCellMin + $gridSize;
            for ($j = 0; $j < $yGrids; $j++) {
                $yCellMin = $yMin + $j * $gridSize;
                $yCellMax = $yCellMin + $gridSize;

                $zValues = [];
                foreach ($points as $p) {
                    if ($p['x'] >= $xCellMin && $p['x'] < $xCellMax &&
                        $p['y'] >= $yCellMin && $p['y'] < $yCellMax) {
                        $zValues[] = $p['z'];
                    }
                }
                if (!empty($zValues)) {
                    $avgZ = array_sum($zValues) / count($zValues);
                    // Volume = luas sel * tinggi (asumsi tanah di z=0)
                    $height = max(0, $avgZ);
                    $totalVolume += $cellArea * $height;
                }
            }
        }
        return round($totalVolume, 3);
    }

    /**
     * Hitung panjang, lebar, dan tinggi rata-rata dari point cloud area bak.
     */
    public function getDimensions(array $points): array
    {
        if (empty($points)) {
            return ['length' => 0, 'width' => 0, 'height_avg' => 0];
        }
        $xs = array_column($points, 'x');
        $ys = array_column($points, 'y');
        $zs = array_column($points, 'z');
        return [
            'length' => max($xs) - min($xs),
            'width'  => max($ys) - min($ys),
            'height_avg' => array_sum($zs) / count($zs),
        ];
    }
}