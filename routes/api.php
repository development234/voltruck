<?php
use App\Http\Controllers\Api\LidarDataController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/lidar-data', [LidarDataController::class, 'store']);
    Route::get('/lidar-data', [LidarDataController::class, 'index']);
    Route::get('/lidar-data/{id}', [LidarDataController::class, 'show']);

    // Endpoint test
    Route::post('/test', function (Request $req) {
        return response()->json([
            'message' => 'OK',
            'received' => $req->all()
        ]);
    });

});

Route::get('/ping', function () {
    return response()->json(['pong' => true]);
});
