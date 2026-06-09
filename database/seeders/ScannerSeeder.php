<?php

namespace Database\Seeders;

use App\Models\Scanner;
use App\Models\User;
use App\Models\Truck;
use Illuminate\Database\Seeder;

class ScannerSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('role', 'user')->first();
        $truck = Truck::first();

        if ($user && $truck) {
            Scanner::create([
                'user_id' => $user->id,
                'truck_id' => $truck->id,
                'scanned_at' => now(),
                'result_volume_m3' => 4.25,
                'notes' => 'Scan contoh',
            ]);
        }
    }
}