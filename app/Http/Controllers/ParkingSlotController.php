<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ParkingSlot;
use App\Models\ParkingArea;
use Illuminate\Support\Str;

class ParkingSlotController extends Controller
{
    /**
     * Ambil semua slot (untuk peta).
     */
    public function index()
    {
        $slots = ParkingSlot::with('area')->get();
        return response()->json($slots);
    }

    /**
     * Simpan slot baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255', // dari form input
            'area_id' => 'required|exists:parking_areas,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $slot = ParkingSlot::create([
            'slot_code' => 'SLOT-' . strtoupper(Str::random(6)),
            'slot_name' => $request->name,
            'area_id' => $request->area_id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'status' => 'available',
        ]);

        return response()->json([
            'message' => 'Slot berhasil disimpan',
            'data' => $slot,
        ]);
    }

    public function storeMultiple(Request $request)
    {
        $request->validate([
            'area_id' => 'required|exists:parking_areas,id',
            'slots' => 'required|array|min:1',
            'slots.*.latitude' => 'required|numeric',
            'slots.*.longitude' => 'required|numeric',
            'slots.*.slot_name' => 'required|string'
        ]);

        $created = [];

        foreach ($request->slots as $slot) {
            $created[] = ParkingSlot::create([
                'slot_code' => 'SLOT-' . strtoupper(Str::random(6)),
                'slot_name' => $slot['slot_name'],
                'area_id' => $request->area_id,
                'latitude' => $slot['latitude'],
                'longitude' => $slot['longitude'],
                'status' => 'available'
            ]);
        }

        return response()->json([
            'message' => 'Semua slot berhasil disimpan',
            'count' => count($created),
            'data' => $created
        ]);
    }


    /**
     * Hapus slot berdasarkan ID.
     */
    public function destroy($id)
    {
        $slot = ParkingSlot::findOrFail($id);
        $slot->delete();

        return response()->json(['message' => 'Slot berhasil dihapus']);
    }
}
