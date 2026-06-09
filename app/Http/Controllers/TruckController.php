<?php

namespace App\Http\Controllers;

use App\Models\Truck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TruckController extends Controller
{
    // Jangan tambahkan method __construct atau middleware() di sini

    public function index()
    {
        $trucks = Truck::orderBy('created_at', 'desc')->paginate(10);
        return view('trucks.index', compact('trucks'));
    }

    public function create()
    {
        return view('trucks.form');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plate_number' => 'required|string|unique:trucks',
            'name' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'length_m' => 'required|numeric|min:0.1',
            'width_m' => 'required|numeric|min:0.1',
            'height_m' => 'required|numeric|min:0.1',
            'capacity_m3' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Truck::create($validator->validated());

        return redirect()->route('trucks.index')->with('success', 'Truk berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $truck = Truck::findOrFail($id);
        return view('trucks.form', compact('truck'));
    }

    public function update(Request $request, $id)
    {
        $truck = Truck::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'plate_number' => 'required|string|unique:trucks,plate_number,' . $id,
            'name' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'length_m' => 'required|numeric|min:0.1',
            'width_m' => 'required|numeric|min:0.1',
            'height_m' => 'required|numeric|min:0.1',
            'capacity_m3' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $truck->update($validator->validated());

        return redirect()->route('trucks.index')->with('success', 'Truk berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $truck = Truck::findOrFail($id);
        $truck->delete();

        return redirect()->route('trucks.index')->with('success', 'Truk berhasil dihapus.');
    }
}