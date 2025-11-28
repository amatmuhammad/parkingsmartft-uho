<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('vehicles')->insert([
            [
                'plate_number' => 'DT 1234 FF',
                'vehicle_type' => 'car',
                'user_id' => 1, // Andi Wijaya
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'plate_number' => 'DT 5678 GH',
                'vehicle_type' => 'car',
                'user_id' => 2, // Siti Rahma
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'plate_number' => 'DT 2468 JK',
                'vehicle_type' => 'motorcycle',
                'user_id' => 3, // Budi Santoso
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'plate_number' => 'DT 9753 LM',
                'vehicle_type' => 'car',
                'user_id' => 4, // Dewi Lestari
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'plate_number' => 'DT 8642 NO',
                'vehicle_type' => 'motorcycle',
                'user_id' => 5, // Rudi Hartono
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
