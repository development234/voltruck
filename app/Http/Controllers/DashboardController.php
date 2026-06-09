<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Truck;
use App\Models\Driver;
use App\Models\Scanner;

class DashboardController extends Controller
{
    public function userDashboard()
    {
        $user = Auth::user();
        $measurements = $user->volumeMeasurements()
            ->orderBy('measured_at', 'desc')
            ->paginate(6);
        $totalVolume = $user->volumeMeasurements()->sum('volume_m3');
        $totalMeasurements = $user->volumeMeasurements()->count();


        return view('dashboard.user', compact(
            'measurements', 
            'totalVolume', 
            'totalMeasurements',

        ));
    }

    // ========== PROFILE USER ==========
    public function profile()
    {
        $user = Auth::user();
        $scanners = Scanner::with(['truck', 'driver'])
                ->where('user_id', $user->id)
                ->orderBy('scanned_at', 'desc')
                ->paginate(5);
        $measurements = $user->volumeMeasurements()->orderBy('measured_at', 'desc')->paginate(5);
        return view('profile.profile', compact('user', 'scanners','measurements'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return redirect()->route('profile')->with('success', 'Profil berhasil diperbarui.');
    }

    public function destroyAccount()
    {
        $user = Auth::user();
        if ($user->role === 'admin') {
            // Jangan hapus admin jika hanya tersisa satu? Atau boleh, tapi hati-hati.
            // Di sini kita tetap izinkan admin menghapus dirinya sendiri.
        }
        Auth::logout();
        $user->delete();

        return redirect('/login')->with('status', 'Akun Anda telah dihapus.');
    }

    public function userScanner()
    {
        $user = Auth::user();
        $scanners = $user->scanners()
            ->with(['truck', 'driver'])
            ->orderBy('scanned_at', 'desc')
            ->paginate(10);

        return view('dashboard.user_scanner', compact('scanners'));
    }

    // app/Http/Controllers/DashboardController.php

    public function destroyUserScanner(Scanner $scanner)
    {
        // Pastikan hanya user pemilik yang bisa menghapus
        if ($scanner->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        $scanner->delete();
        return redirect()->route('dashboard.user.scanner')->with('success', 'Data scanner berhasil dihapus.');
    }

}