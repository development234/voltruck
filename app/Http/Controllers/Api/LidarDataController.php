<?php
// app/Http/Controllers/Api/LidarDataController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LidarRawData;
use App\Jobs\ProcessLidarDataJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LidarDataController extends Controller
{
    /**
     * Menerima data mentah dari sensor LiDAR
     */
    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'sensor_id' => 'required|string|max:100',
            'raw_data'   => 'required|json', // data mentah dalam format JSON string
            'measured_at' => 'required|date_format:Y-m-d H:i:s',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Ambil user yang sedang login (asumsi API menggunakan token auth)
        // Jika tidak pakai auth, bisa lewat parameter user_id dengan validasi tambahan
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthenticated'
            ], 401);
        }

        // Simpan data mentah
        $rawData = LidarRawData::create([
            'user_id'     => $user->id,
            'sensor_id'   => $request->sensor_id,
            'raw_data'    => $request->raw_data, // kolom json otomatis di-encode/decode
            'measured_at' => $request->measured_at,
        ]);

        // Dispatch job ke queue untuk diproses
        ProcessLidarDataJob::dispatch($rawData);

        return response()->json([
            'status' => 'success',
            'message' => 'Data LiDAR diterima, sedang diproses',
            'data' => [
                'id' => $rawData->id,
                'measured_at' => $rawData->measured_at,
            ]
        ], 202); // 202 Accepted
    }

    /**
     * Ambil riwayat pengukuran user (opsional)
     */
    public function history(Request $request)
    {
        $user = Auth::user();
        $measurements = LidarRawData::with('volumeMeasurement')
            ->where('user_id', $user->id)
            ->orderBy('measured_at', 'desc')
            ->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $measurements
        ]);
    }
}