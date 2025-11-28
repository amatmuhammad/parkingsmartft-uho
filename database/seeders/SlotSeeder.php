<?php
// database/seeders/SlotSeeder.php
namespace Database\Seeders;

use App\Models\Slot;
use Illuminate\Database\Seeder;

class SlotSeeder extends Seeder
{
    public function run()
    {
        // Clear existing slots
        Slot::truncate();

        // Slots untuk motor (M series)
        $motorSlots = [
            ['M001', 'Area Motor Utara', -6.175100, 106.827100],
            ['M002', 'Area Motor Utara', -6.175101, 106.827101],
            ['M003', 'Area Motor Utara', -6.175102, 106.827102],
            ['M004', 'Area Motor Selatan', -6.175200, 106.827200],
            ['M005', 'Area Motor Selatan', -6.175201, 106.827201],
            ['M006', 'Area Motor Selatan', -6.175202, 106.827202],
            ['M007', 'Area Motor Timur', -6.175300, 106.827300],
            ['M008', 'Area Motor Timur', -6.175301, 106.827301],
            ['M009', 'Area Motor Barat', -6.175400, 106.827400],
            ['M010', 'Area Motor Barat', -6.175401, 106.827401],
        ];

        foreach ($motorSlots as $slot) {
            Slot::create([
                'slot_number' => $slot[0],
                'area' => $slot[1],
                'vehicle_type' => 'roda_dua',
                'latitude' => $slot[2],
                'longitude' => $slot[3],
                'is_available' => true,
                'description' => 'Slot parkir khusus motor'
            ]);
        }

        // Slots untuk mobil (C series)
        $mobilSlots = [
            ['C001', 'Area Mobil Utara', -6.176100, 106.828100],
            ['C002', 'Area Mobil Utara', -6.176101, 106.828101],
            ['C003', 'Area Mobil Utara', -6.176102, 106.828102],
            ['C004', 'Area Mobil Selatan', -6.176200, 106.828200],
            ['C005', 'Area Mobil Selatan', -6.176201, 106.828201],
            ['C006', 'Area Mobil Timur', -6.176300, 106.828300],
            ['C007', 'Area Mobil Timur', -6.176301, 106.828301],
            ['C008', 'Area Mobil Barat', -6.176400, 106.828400],
        ];

        foreach ($mobilSlots as $slot) {
            Slot::create([
                'slot_number' => $slot[0],
                'area' => $slot[1],
                'vehicle_type' => 'roda_empat',
                'latitude' => $slot[2],
                'longitude' => $slot[3],
                'is_available' => true,
                'description' => 'Slot parkir khusus mobil'
            ]);
        }

        // Slots untuk kedua jenis kendaraan (B series)
        $bothSlots = [
            ['B001', 'Area Campuran', -6.175500, 106.827500],
            ['B002', 'Area Campuran', -6.175501, 106.827501],
            ['B003', 'Area Campuran', -6.175502, 106.827502],
        ];

        foreach ($bothSlots as $slot) {
            Slot::create([
                'slot_number' => $slot[0],
                'area' => $slot[1],
                'vehicle_type' => 'both',
                'latitude' => $slot[2],
                'longitude' => $slot[3],
                'is_available' => true,
                'description' => 'Slot parkir untuk motor dan mobil'
            ]);
        }

        $this->command->info('Slots created successfully!');
        $this->command->info('Motor slots: ' . count($motorSlots));
        $this->command->info('Mobil slots: ' . count($mobilSlots));
        $this->command->info('Both slots: ' . count($bothSlots));
    }
}