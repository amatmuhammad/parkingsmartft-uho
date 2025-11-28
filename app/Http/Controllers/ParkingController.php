<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Parking;
use App\Models\Reservation;
use Illuminate\Http\Request;


class ParkingController extends Controller
{
    //
    public function getActiveParkings()
{
    $reservations = Reservation::with(['user', 'vehicle', 'slot'])
                              ->where('status', 'active')
                              ->get();
    
    // dd($reservations);

    return response()->json($reservations);
}

public function scanOutByToken(Request $request, $token)
{
    $reservation = Reservation::where('qrcode_token', $token)->first();

    if (!$reservation) {
        return response()->json([
            'success' => false,
            'message' => 'QR Code tidak valid'
        ], 404);
    }

    if ($reservation->status !== 'active') {
        return response()->json([
            'success' => false,
            'message' => 'Reservasi belum diaktifkan atau sudah selesai'
        ], 400);
    }

    $parking = Parking::where('reservation_id', $reservation->id)
                      ->where('status', 'ongoing')
                      ->first();

    if (!$parking) {
        return response()->json([
            'success' => false,
            'message' => 'Data parkir tidak ditemukan'
        ], 404);
    }

    // 🔥 PERBAIKAN UTAMA — ubah ke Carbon
    $start_time = Carbon::parse($parking->start_time);
    $end_time = Carbon::now();
    $duration = $start_time->diffInMinutes($end_time);

    $parking->update([
        'end_time' => $end_time,
        'duration' => $duration,
        'status'   => 'completed',
    ]);

    $reservation->update([
        'status'   => 'completed',
        'end_time' => $end_time,
    ]);

    if ($reservation->slot) {
        $reservation->slot->update(['status' => 'available']);
    }

    return response()->json([
        'success' => true,
        'message' => 'Scan out berhasil, parkir selesai',
        'parking' => [
            'id' => $parking->id,
            'reservation_id' => $parking->reservation_id,
            'user_id' => $parking->user_id,
            'vehicle_id' => $parking->vehicle_id,
            'slot_id' => $parking->slot_id,
            'start_time' => $parking->start_time,
            'end_time' => $parking->end_time,
            'duration' => $parking->duration . ' menit',
            'status' => $parking->status,
        ]
    ]);
}
}
