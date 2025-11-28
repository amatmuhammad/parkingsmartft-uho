<?php

namespace App\Http\Controllers;

use App\Models\ParkingArea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ParkingAreaController extends Controller
{

    public function index()
    {
        $areas = ParkingArea::all();
        return response()->json($areas);
        // return view('administrator.maps-slot');
    }

    public function maps(){

        $gates = [
            '1',
            '2'
        ];

        return view('administrator.maps-slot', compact('gates'));
    }

    public function json()
    {
        return response()->json(ParkingArea::all());
    }

    public function store(Request $request)
    {
        Log::info('Request data:', $request->all()); // log semua input
        
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'gate' => 'required|string',
            'type' => 'required|in:car,motorcycle',
            'polygon_coordinates' => 'required|array'
        ]);
        // dd($validated);

        $area = ParkingArea::create([
            'name' => $validated['name'],
            'gate' => $validated['gate'],
            'type' => $validated['type'],
            // Simpan sebagai JSON string tunggal
            'polygon_coordinates' => json_encode($validated['polygon_coordinates'])
        ]);

        


        return response()->json($area, 201);
    }


    public function update(Request $request, $id)
    {
        $area = ParkingArea::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:100',
            'gate' => 'required|string|max:100',
            'type' => 'sometimes|in:car,motorcycle',
            'polygon_coordinates' => 'sometimes|array'
        ]);

        $area->update($validated);

        return response()->json($area);
    }

    public function destroy($id)
    {
        $area = ParkingArea::findOrFail($id);
        $area->delete();
        return response()->json(['message' => 'Area deleted successfully']);
    }


    public function list()
    {
        $areas = ParkingArea::withCount('slots')->get();

        $areas = $areas->map(function ($area) {

            // decode JSON string
            $coords = json_decode($area->polygon_coordinates, true);

            // ubah string menjadi float
            $coords = array_map(function($pair){
                return [
                    floatval($pair[0]),
                    floatval($pair[1])
                ];
            }, $coords);

            return [
                'id' => $area->id,
                'name' => $area->name,
                'gate' => $area->gate,
                'type' => $area->type,
                'polygon_coordinates' => $coords,   // sudah bersih
                'total_slots' => $area->slots_count
            ];
        });

        return response()->json($areas);
    }

}
