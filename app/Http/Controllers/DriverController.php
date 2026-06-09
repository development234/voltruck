<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DriverController extends Controller
{
    public function index()
    {
        $drivers = Driver::orderBy('created_at', 'desc')->paginate(10);
        return view('drivers.index', compact('drivers'));
    }

    public function create()
    {
        return view('drivers.form');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'license_number' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Driver::create($validator->validated());

        return redirect()->route('drivers.index')->with('success', 'Pengemudi berhasil ditambahkan.');
    }

    public function edit(Driver $driver)
    {
        return view('drivers.form', compact('driver'));
    }

    public function update(Request $request, Driver $driver)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'license_number' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $driver->update($validator->validated());

        return redirect()->route('drivers.index')->with('success', 'Data pengemudi berhasil diperbarui.');
    }

    public function destroy(Driver $driver)
    {
        $driver->delete();

        return redirect()->route('drivers.index')->with('success', 'Pengemudi berhasil dihapus.');
    }
}