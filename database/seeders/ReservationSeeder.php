<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vehicle;
use App\Models\ParkingSlot;
use App\Models\Reservation;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ReservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // pastikan data referensi ada
        DB::table('reservations')->insert([
            [
                'user_id' => 1,
                'vehicle_id' => 1,
                'slot_id' => 33,
                'qrcode_token' => Str::uuid(),
                'expired_at' => Carbon::now()->addHours(2),
                'start_time' => Carbon::now(),
                'end_time' => Carbon::now()->addHours(1),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'vehicle_id' => 2,
                'slot_id' => 32,
                'qrcode_token' => Str::uuid(),
                'expired_at' => Carbon::now()->addHours(3),
                'start_time' => Carbon::now()->subHour(),
                'end_time' => Carbon::now()->addHour(),
                'status' => 'booked',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'vehicle_id' => 3,
                'slot_id' => 31,
                'qrcode_token' => Str::uuid(),
                'expired_at' => Carbon::now()->addHours(1),
                'start_time' => Carbon::now()->subHours(2),
                'end_time' => Carbon::now()->subHour(),
                'status' => 'completed',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 4,
                'vehicle_id' => 4,
                'slot_id' => 30,
                'qrcode_token' => Str::uuid(),
                'expired_at' => Carbon::now()->addMinutes(30),
                'start_time' => null,
                'end_time' => null,
                'status' => 'booked',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 5,
                'vehicle_id' => 5,
                'slot_id' => 29,
                'qrcode_token' => Str::uuid(),
                'expired_at' => Carbon::now()->subMinutes(10),
                'start_time' => Carbon::now()->subHours(3),
                'end_time' => Carbon::now()->subHours(2),
                'status' => 'expired',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
