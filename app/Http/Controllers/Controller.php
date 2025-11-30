<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Parking;
use App\Models\Vehicle;
use App\Models\ParkingArea;
use App\Models\ParkingSlot;
use App\Models\Reservation;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function indexuser(){

        $user = auth()->user();

        return view('user.index', [
            'vehicles' => Vehicle::where('user_id', $user->id)->get(),
            'slots'    => ParkingSlot::where('status', 'available')->get(),
            'history'  => Reservation::where('user_id', $user->id)
                            ->latest()
                            ->get()
        ]);

         
    }

    

    public function getSlots($area_id)
    {
        return ParkingSlot::where('area_id', $area_id)
            ->where('status', 'available')
            ->get();
    }

     // json data table reservasi
    public function json()
    {
        $reservations = Reservation::with(['slot.area', 'vehicle'])
            ->where('user_id', auth()->id())
            ->get();

        $data = $reservations->map(function($r){
            return [
                'id' => $r->id,
                'status' => $r->status,
                'slot' => $r->slot ? [
                    'slot_name' => $r->slot->name,
                    'area_name' => $r->slot->area->name ?? '-'
                ] : null,
                'vehicle' => $r->vehicle ? [
                    'plate_number' => $r->vehicle->plate_number
                ] : null
            ];
        });

        return response()->json($data);
    }

    public function historyAjax()
    {
         $user = auth()->user();

        $history = Reservation::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($history);
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'slot_id' => 'required|exists:parking_slots,id',
        ]);

        DB::beginTransaction();
        try {
            // Cek reservasi aktif user
            $existing = Reservation::where('user_id', auth()->id())
                        ->whereIn('status', ['booked','active'])
                        ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda masih memiliki reservasi aktif.'
                ]);
            }

            // Cek slot masih available?
            $slot = ParkingSlot::find($request->slot_id);
            if ($slot->status !== 'available') {
                return response()->json([
                    'success' => false,
                    'message' => 'Slot tidak tersedia.'
                ]);
            }

            // Generate token
            $token = Str::uuid()->toString();
            $tokenOut = Str::uuid()->toString();
            $expiredAt = Carbon::now()->addMinutes(5);

            // Simpan reservasi
            $reservation = Reservation::create([
                'user_id' => auth()->id(),
                'vehicle_id' => $request->vehicle_id,
                'slot_id' => $request->slot_id,
                'qrcode_token' => $token,
                'qrcode_out' => $tokenOut,
                'expired_at' => $expiredAt,
                'status' => 'booked'
            ]);

            // Update slot
            $slot->update(['status' => 'booked']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Reservasi berhasil dibuat!',
                'qrcode_token' => $token,
                'qrcode_out' => $tokenOut,
                'data' => $reservation
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$e->getMessage()]);
        }
    }


    public function scan($token)
    {
        $reservation = Reservation::with(['vehicle', 'slot'])->where('qrcode_token', $token)->first();

        if (!$reservation) {
            return view('scan.result', [
                'success' => false,
                'message' => 'QR tidak valid'
            ]);
        }

        if (now()->greaterThan($reservation->expired_at)) {
            $reservation->update(['status' => 'expired']);
            $reservation->slot->update(['status' => 'available']);

            return view('scan.result', [
                'success' => false,
                'message' => 'QR sudah expired'
            ]);
        }

        $parking = Parking::where('reservation_id', $reservation->id)
                        ->where('status', 'ongoing')
                        ->first();    

        if ($reservation->status === 'active') {
            return view('scan.result', [
                'success' => true,
                'message' => 'Terima Kasih , Anda sudah scan masuk sebelumnya!',
                'reservation' => $reservation,
                'parking' => $parking
            ]);
        }

        // Activate parking
        $reservation->update([
            'status' => 'active',
            'start_time' => now()
        ]);

        $parking = Parking::create([
            'reservation_id' => $reservation->id,
            'user_id' => $reservation->user_id,
            'vehicle_id' => $reservation->vehicle_id,
            'slot_id' => $reservation->slot_id,
            'start_time' => now(),
            'status' => 'ongoing',
        ]);

        $reservation->slot->update(['status' => 'occupied']);

        return view('scan.result', [
            'success' => true,
            'message' => 'Scan berhasil! Parkir dimulai',
            'reservation' => $reservation,
            'parking' => $parking
        ]);
    }



    public function scanOut($tokenOut)
    {
        // Ambil reservasi berdasarkan token OUT
        $reservation = Reservation::where('qrcode_out', $tokenOut)->first();

        // Jika tidak ditemukan
        if (!$reservation) {
            return view('scan.out', ['error' => 'QR tidak valid']);
        }

        // Jika belum scan IN
        if ($reservation->status !== 'active' && $reservation->status !== 'completed') {
            return view('scan.out', ['error' => 'Anda belum melakukan Scan IN.']);
        }

        if ($reservation->status === 'completed') {

            $parking = Parking::where('reservation_id', $reservation->id)->first();

            if ($parking) {
                $start = Carbon::parse($parking->start_time);
                $end   = Carbon::parse($parking->end_time);

                // Hitung ulang durasi
                $durationMinutes = $start->diffInMinutes($end);
                $hours   = floor($durationMinutes / 60);
                $minutes = $durationMinutes % 60;

                $durationDisplay = "{$hours} jam {$minutes} menit";

                return view('scan.out', [
                    'data' => [
                        'slot_name'        => $reservation->slot->slot_name,
                        'start_time'       => $start,
                        'end_time'         => $end,
                        'duration_display' => $durationDisplay,
                        'total_fee'        => $parking->total_fee,
                    ]
                ]);
            }

            return view('scan.out', ['error' => 'Data parkir tidak ditemukan']);
        }

        $parking = Parking::where('reservation_id', $reservation->id)
                            ->where('status', 'ongoing')
                            ->first();

        if (!$parking) {
            return view('scan.out', ['error' => 'Data parkir tidak ditemukan']);
        }

        // Ambil waktu mulai & waktu selesai
        $start = Carbon::parse($parking->start_time);
        $end   = now();

        // Hitung durasi menit
        $durationMinutes = $start->diffInMinutes($end);

        // Konversi ke jam + menit
        $hours   = floor($durationMinutes / 60);
        $minutes = $durationMinutes % 60;

        $durationDisplay = "{$hours} jam {$minutes} menit";

        // Hitung tarif (2000 per menit, bisa kamu ubah)
        $fee = $durationMinutes * 2000;

        // Update tabel Parking
        $parking->update([
            'end_time'  => $end,
            'duration'  => $durationMinutes,
            'total_fee' => $fee,
            'status'    => 'completed',
        ]);

        // Update reservation
        $reservation->update([
            'status'   => 'completed',
            'end_time' => $end
        ]);

        // Set slot menjadi available lagi
        $reservation->slot->update(['status' => 'available']);

        return view('scan.out', [
            'data' => [
                'slot_name'        => $reservation->slot->slot_name,
                'start_time'       => $start,
                'end_time'         => $end,
                'duration_display' => $durationDisplay,
                'total_fee'        => $fee,
            ]
        ]);
    }

    // public function getSlotsByVehicle($vehicleId)
    // {
    //     $vehicle = Vehicle::find($vehicleId);

    //     if (!$vehicle) {
    //         return response()->json([]);
    //     }

    //     $type = $vehicle->vehicle_type ?? null;

    //     if (!$type) {
    //         return response()->json([]);
    //     }

    //     $slot = ParkingSlot::with('area')
    //         ->whereHas('area', function ($q) use ($type) {
    //             $q->where('type', $type);
    //         })
    //         ->where('status', 'available')
    //         ->orderBy('slot_name')
    //         ->first();

    //     return response()->json($slot ? [$slot] : []); // array agar aman di frontend user
    // }

    public function getSlotsByVehicle($vehicleId)
    {
        $vehicle = Vehicle::find($vehicleId);

        if (!$vehicle) {
            return response()->json([]);
        }

        // Ambil type area berdasarkan vehicle_type
        $area = ParkingArea::where('type', $vehicle->vehicle_type)->first();

        if (!$area) {
            return response()->json([]);
        }

        // Ambil 1 slot paling awal (ascending)
        $slot = ParkingSlot::where('area_id', $area->id)
            ->where('status', 'available')
            ->orderBy('slot_name', 'asc')
            ->first();

        if (!$slot) {
            return response()->json([]);
        }

        // Return dengan type dari tabel parking_area
        return response()->json([
            [
                'id'        => $slot->id,
                'slot_name' => $slot->slot_name,
                'status'    => $slot->status,
                'type'      => $area->type,   // ← INI type dari parking_area
            ]
        ]);
    }





    public function profile(){

        $vehicles = auth()->user()->vehicles;

        return view('user.profile', compact('vehicles'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'phone' => 'required|numeric',
            'password' => 'nullable|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // update nomor hp
        $user->phone = $request->phone;

        // update password jika diisi
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Phone number & password updated successfully!'
        ]);
    }

    public function deleteAccount()
    {
        $user = Auth::user();
        Auth::logout();
        // $user->delete();

        return redirect('/login')->with('success', 'Account deleted successfully.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'plate_number' => 'required|unique:vehicles',
            'vehicle_type' => 'required|in:car,motorcycle'
        ]);

        $vehicle = Vehicle::create([
            'plate_number' => $request->plate_number,
            'vehicle_type' => $request->vehicle_type,
            'user_id'      => Auth::id()
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Vehicle added successfully',
            'data'    => $vehicle
        ]);
    }

    public function destroy($id)
    {
        $vehicle = Vehicle::where('id', $id)
                          ->where('user_id', Auth::id())
                          ->first();

        if (!$vehicle) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $vehicle->delete();

        return response()->json(['message' => 'Vehicle deleted']);
    }

}
