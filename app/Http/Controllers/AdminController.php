<?php

namespace App\Http\Controllers;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\LidarDataController;
use App\Http\Controllers\Api\TruckController;
use App\Http\Controllers\Api\ScannerController;
use App\Http\Controllers\Api\DriverController;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Truck;
use App\Models\Scanner;
use App\Models\Driver;
use App\Models\VolumeMeasurement;


class AdminController extends Controller
{
    public function adminDashboard()
    {
        // Contoh data statistik
        $totalUsers = User::count();
        $totalAdmins = User::where('role', 'admin')->count();
        $users = User::paginate(5);
        $totalTrucks = Truck::count(); // <-- tambahkan ini
        // Ambil semua pengukuran dengan relasi user
        $measurements = VolumeMeasurement::with('user')
            ->orderBy('measured_at', 'desc')
            ->paginate(5);
        
        $totalVolumeAll = VolumeMeasurement::sum('volume_m3');
        $totalMeasurementsAll = VolumeMeasurement::count();
        $totalVolumeScan = Scanner::sum('result_volume_m3');
        $scanners = Scanner::with(['user', 'truck'])->latest()->paginate(10);
        
        return view('dashboard.admin', compact(
            'users',
            'totalTrucks',
            'totalUsers', 
            'totalAdmins', 
            'measurements',
            'scanners',
            'totalVolumeAll',
            'totalMeasurementsAll',
            'totalVolumeScan',
        ));
    }

    // Di dalam class AdminController, tambahkan:
    public function usersIndex()
    {
        $users = User::paginate(10);
        return view('dashboard.admin', compact('users'));
    }

    public function editUser(User $user)
    {
        return view('admin.users_edit', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:user,admin',
        ]);

        $user->update($request->only('name', 'email', 'role'));

        return redirect()->route('dashboard.admin')->with('success', 'User berhasil diupdate.');
    }

    public function scannerIndex()
    {
        $scanners = Scanner::with(['user', 'truck', 'driver'])
            ->orderBy('scanned_at', 'desc')
            ->paginate(15);
        
        $totalVolumeScan = Scanner::sum('result_volume_m3');
        $totalScanCount = Scanner::count();
        $trucks = Truck::orderBy('plate_number')->get();
        $drivers = Driver::orderBy('name')->get();
        
        return view('admin.scanners', compact(
            'scanners', 
            'totalVolumeScan', 
            'totalScanCount', 
            'trucks', 
            'drivers',
        ));
    }


    public function scannerEdit($id)
    {
        $scanner = Scanner::with('truck', 'driver')->findOrFail($id);
        return response()->json($scanner);
    }

    public function scannerUpdate(Request $request, $id)
    {
        $scanner = Scanner::findOrFail($id);
        $request->validate([
            'truck_id' => 'required|exists:trucks,id',
            'driver_id' => 'nullable|exists:drivers,id',
            'result_volume_m3' => 'required|numeric|min:0',
            'scanned_at' => 'required|date',
            'notes' => 'nullable|string',
        ]);
        $scanner->update($request->only(['truck_id', 'driver_id', 'result_volume_m3', 'scanned_at', 'notes']));
        return redirect()->route('admin.scanners')->with('success', 'Data scanner berhasil diupdate.');
    }

    public function scannerDestroy($id)
    {
        $scanner = Scanner::findOrFail($id);
        $scanner->delete();
        return redirect()->route('admin.scanners')->with('success', 'Data scanner berhasil dihapus.');
    }


    public function destroyUser(User $user)
    {
        // Cegah delete user sendiri
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menghapus akun sendiri.');
        }
        $user->delete();
        return redirect()->route('dashboard.admin')->with('success', 'User berhasil dihapus.');
    }

}