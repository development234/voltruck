<?php

namespace App\Http\Controllers;

use App\Models\Scanner;
use App\Models\Truck;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ScannerController extends Controller
{
    public function index()
    {
        $scanners = Scanner::with(['user', 'truck', 'driver'])
            ->where('user_id', Auth::id())
            ->orderBy('scanned_at', 'desc')
            ->paginate(10);
        $trucks = Truck::orderBy('plate_number')->get();
        $drivers = Driver::orderBy('name')->get();
        
        // Konversi ke array biasa agar aman di json_encode
        $trucksData = $trucks->map(function($t) {
            return [
                'id' => $t->id,
                'length' => (float)$t->length_m,
                'width' => (float)$t->width_m,
                'height' => (float)$t->height_m,
            ];
        })->values()->toArray();

        return view('scanner.index', compact('scanners', 'trucks', 'drivers', 'trucksData'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'truck_id' => 'required|exists:trucks,id',
            'driver_id' => 'nullable|exists:drivers,id',
            'result_volume_m3' => 'required|numeric|min:0',
            'scanned_at' => 'required|date',
            'notes' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Scanner::create([
            'user_id' => Auth::id(),
            'truck_id' => $request->truck_id,
            'driver_id' => $request->driver_id,
            'scanned_at' => $request->scanned_at,
            'result_volume_m3' => $request->result_volume_m3,
            'notes' => $request->notes,
        ]);

        return redirect()->route('scanner.index')->with('success', 'Data scanning berhasil disimpan.');
    }

    public function destroy(Scanner $scanner)
    {
        if ($scanner->user_id !== Auth::id()) {
            abort(403);
        }
        $scanner->delete();
        return redirect()->route('scanner.index')->with('success', 'Data scanning berhasil dihapus.');
    }
}