<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Parking;
use App\Models\Vehicle;
use App\Models\ParkingSlot;
use App\Models\Reservation;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ReservationController extends Controller
{
    public function index()
    {
        try {
            // Hapus filter status jika kolom tidak ada
            $reservations = Reservation::with(['user', 'vehicle', 'slot'])
                ->latest()
                ->get();

            $users = User::all(); // Hapus where('status', 'active')
            $vehicles = Vehicle::with('user')->get();
            $slots = ParkingSlot::with('parking_area')->get();

            Log::info("📊 Data loaded for index:", [
                'reservations_count' => $reservations->count(),
                'users_count' => $users->count(),
                'vehicles_count' => $vehicles->count(),
                'slots_count' => $slots->count()
            ]);

            return view('reservations.index', compact('reservations', 'users', 'vehicles', 'slots'));

        } catch (\Exception $e) {
            Log::error("❌ [ReservationController@index] Error: " . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data reservasi.');
        }
    }

    public function getSlotsByVehicle($vehicleId)
    {
        $vehicle = Vehicle::find($vehicleId);

        if (!$vehicle) {
            return response()->json([]);
        }
        
        $type = $vehicle->vehicle_type;

        $slots = ParkingSlot::with('area')
            ->whereHas('area', function ($q) use ($type) {
                $q->where('type', $type);
            })
            ->where('status', 'available')
            ->orderBy('slot_name')
            ->get();

        return response()->json($slots);
    }


    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            Log::info("🟢 [ReservationController@store] Data masuk:", $request->all());

            // VALIDASI DENGAN DEBUG
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'vehicle_id' => 'required|exists:vehicles,id',
                'slot_id' => 'required|exists:parking_slots,id',
            ]);

            if ($validator->fails()) {
                Log::error("❌ Validasi gagal:", $validator->errors()->toArray());
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();
            Log::info("✅ Data valid:", $validated);

            // CEK USER
            $user = User::find($request->user_id);
            if (!$user) {
                Log::error("❌ User tidak ditemukan:", ['user_id' => $request->user_id]);
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan.',
                ], 422);
            }
            Log::info("✅ User ditemukan:", ['user_id' => $user->id, 'name' => $user->name]);

            // CEK KENDARAAN
            $vehicle = Vehicle::with('user')->find($request->vehicle_id);
            Log::info("🔍 Data kendaraan:", [
                'vehicle' => $vehicle ? [
                    'id' => $vehicle->id,
                    'plate_number' => $vehicle->plate_number,
                    'vehicle_type' => $vehicle->vehicle_type,
                    'user_id' => $vehicle->user_id,
                    'user_name' => $vehicle->user->name ?? 'No Owner'
                ] : 'NULL',
                'request_vehicle_id' => $request->vehicle_id
            ]);
            
            if (!$vehicle) {
                Log::error("❌ Kendaraan tidak ditemukan:", ['vehicle_id' => $request->vehicle_id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Kendaraan tidak ditemukan.',
                ], 422);
            }

            // CEK SLOT
            $slot = ParkingSlot::find($request->slot_id);
            Log::info("🔍 Data slot:", [
                'slot' => $slot ? [
                    'id' => $slot->id,
                    'slot_name' => $slot->slot_name,
                    'slot_code' => $slot->slot_code,
                    'status' => $slot->status
                ] : 'NULL',
                'request_slot_id' => $request->slot_id
            ]);
            
            if (!$slot) {
                Log::error("❌ Slot tidak ditemukan:", ['slot_id' => $request->slot_id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Slot parkir tidak ditemukan.',
                ], 422);
            }

            if ($slot->status !== 'available') {
                Log::warning("⚠️ Slot tidak available:", [
                    'slot_status' => $slot->status,
                    'slot_name' => $slot->slot_name
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Slot parkir tidak tersedia. Status: ' . $slot->status,
                ], 422);
            }

            // CEK RESERVASI AKTIF UNTUK USER
            $existingReservation = Reservation::where('user_id', $request->user_id)
                ->whereIn('status', ['booked', 'active'])
                ->first();

            if ($existingReservation) {
                Log::warning("⚠️ User sudah punya reservasi aktif:", [
                    'existing_reservation_id' => $existingReservation->id,
                    'user_id' => $request->user_id
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'User sudah memiliki reservasi aktif.',
                ], 422);
            }

            // CEK RESERVASI AKTIF UNTUK SLOT
            $existingSlotReservation = Reservation::where('slot_id', $request->slot_id)
                ->whereIn('status', ['booked', 'active'])
                ->first();

            if ($existingSlotReservation) {
                Log::warning("⚠️ Slot sudah dipakai reservasi aktif:", [
                    'existing_reservation_id' => $existingSlotReservation->id,
                    'slot_id' => $request->slot_id
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Slot parkir sudah dipakai dalam reservasi aktif.',
                ], 422);
            }

            // BUAT TOKEN UNIK
            $token = Str::uuid()->toString();
            Log::info("🔑 Token generated:", ['token' => $token]);

            // WAKTU
            $now = Carbon::now();
            $expired = $now->copy()->addMinute(5);

            Log::info("⏰ Waktu reservasi:", [
                'now' => $now->format('Y-m-d H:i:s'),
                'expired' => $expired->format('Y-m-d H:i:s')
            ]);

            // INSERT RESERVASI
            $reservationData = [
                'user_id'     => $request->user_id,
                'vehicle_id'  => $request->vehicle_id,
                'slot_id'     => $request->slot_id,
                'qrcode_token'=> $token,
                'expired_at'  => $expired,
                'start_time'  => null,
                'end_time'    => null,
                'status'      => 'booked',
            ];

            Log::info("💾 Data yang akan diinsert:", $reservationData);

            $reservation = Reservation::create($reservationData);

            Log::info("✅ Reservasi created:", [
                'reservation_id' => $reservation->id,
                'inserted_data' => $reservation->toArray()
            ]);

            // UPDATE SLOT
            $slotUpdate = $slot->update([
                'status' => 'booked'
            ]);

            Log::info("🅿️ Slot updated:", [
                'slot_id' => $slot->id,
                'new_status' => 'booked',
                'update_result' => $slotUpdate
            ]);

            DB::commit();

            Log::info("🎉 Reservasi berhasil dibuat", [
                'id' => $reservation->id,
                'user' => $reservation->user_id,
                'vehicle' => $reservation->vehicle_id,
                'slot' => $reservation->slot_id,
                'token' => $token
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Reservasi berhasil dibuat!',
                'qrcode_token' => $token,
                'data' => $reservation
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error("❌ Validation Exception: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("❌ [ReservationController@store] Error: " . $e->getMessage());
            Log::error("📝 Stack trace: " . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function cancel(Reservation $reservation)
    {
        DB::beginTransaction();
        try {
            Log::info("🟡 [ReservationController@cancel] Membatalkan reservasi:", [
                'reservation_id' => $reservation->id,
                'current_status' => $reservation->status
            ]);

            // Hanya bisa cancel reservasi dengan status booked
            if ($reservation->status !== 'booked') {
                Log::warning("⚠️ Tidak bisa cancel reservasi dengan status:", ['status' => $reservation->status]);
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya reservasi dengan status booked yang dapat dibatalkan.'
                ], 422);
            }

            // Update status reservasi
            $reservation->update([
                'status' => 'cancelled',
                'end_time' => Carbon::now()
            ]);

            Log::info("✅ Reservasi dibatalkan:", [
                'reservation_id' => $reservation->id,
                'new_status' => 'cancelled'
            ]);

            // Kembalikan slot parkir ke available
            if ($reservation->slot) {
                $reservation->slot->update([
                    'status' => 'available'
                ]);
                Log::info("🅿️ Slot dikembalikan ke available:", [
                    'slot_id' => $reservation->slot->id,
                    'slot_name' => $reservation->slot->slot_name
                ]);
            }

            DB::commit();

            Log::info("🎉 Reservasi berhasil dibatalkan", ['reservation_id' => $reservation->id]);

            return response()->json([
                'success' => true,
                'message' => 'Reservasi berhasil dibatalkan.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("❌ [ReservationController@cancel] Error: " . $e->getMessage());
            Log::error("📝 Stack trace: " . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ], 500);
        }
    }


    public function scan($token)
    {
        // cari reservasi berdasarkan token
        $reservation = Reservation::where('qrcode_token', $token)->first();

        if (!$reservation) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak valid'
            ], 404);
        }

        // cek expired
        if (now()->greaterThan($reservation->expired_at)) {

            // update status
            $reservation->update(['status' => 'expired']);

            // kembalikan slot
            if ($reservation->slot) {
                $reservation->slot->update(['status' => 'available']);
            }

            return response()->json([
                'success' => false,
                'message' => 'QR Code sudah expired'
            ], 403);
        }

        // jika sudah discan
        if ($reservation->status === 'active') {
            return response()->json([
                'success' => false,
                'message' => 'QR telah digunakan'
            ], 400);
        }

        // update reservasi menjadi aktif
        $reservation->update([
            'status'     => 'active',
            'start_time' => now(),
        ]);

        // buat data parkir
        $parking = Parking::create([
            'reservation_id' => $reservation->id,
            'user_id'        => $reservation->user_id,
            'vehicle_id'     => $reservation->vehicle_id,
            'slot_id'        => $reservation->slot_id,
            'start_time'     => now(),
            'status'         => 'ongoing',
        ]);

        // ubah slot menjadi occupied
        $reservation->slot->update([
            'status' => 'occupied'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Scan berhasil, parkir dimulai',
            'parking' => $parking
        ]);
    }


    public function scanOut($token)
    {
        // cari reservasi berdasarkan token
        $reservation = Reservation::where('qrcode_token', $token)->first();

        if (!$reservation) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak valid'
            ], 404);
        }

        // cek jika status bukan active (belum discan masuk)
        if ($reservation->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Reservasi belum diaktifkan atau sudah selesai'
            ], 400);
        }

        // cek apakah ada data parkir aktif
        $parking = Parking::where('reservation_id', $reservation->id)
                        ->where('status', 'ongoing')
                        ->first();

        if (!$parking) {
            return response()->json([
                'success' => false,
                'message' => 'Data parkir tidak ditemukan'
            ], 404);
        }

        // hitung durasi parkir
        $start_time = $parking->start_time;
        $end_time = now();
        $duration = $start_time->diffInMinutes($end_time); // durasi dalam menit

        // update data parkir
        $parking->update([
            'end_time' => $end_time,
            'duration' => $duration,
            'status'   => 'completed',
        ]);

        // update reservasi
        $reservation->update([
            'status'   => 'completed',
            'end_time' => $end_time,
        ]);

        // kembalikan slot ke available
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



    public function json()
    {
        $data = Reservation::with(['user', 'vehicle', 'slot'])
            ->orderBy('id', 'DESC')
            ->get();

        return response()->json($data);
    }



}