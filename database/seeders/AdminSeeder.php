<?php
// database/seeders/AdminSeeder.php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Cek apakah admin sudah ada
        if (!User::where('email', 'admin@parkir.com')->exists()) {
            // Create Super Admin
            User::create([
                'name' => 'Super Administrator',
                'email' => 'admin@parkir.com',
                'phone' => '081234567890',
                'vehicle_number' => 'B1234ADM',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]);

            $this->command->info('Super Admin created successfully!');
            $this->command->info('Email: admin@parkir.com');
            $this->command->info('Password: admin123');
        } else {
            $this->command->info('Super Admin already exists!');
        }

        // Create Additional Admin Users
        $admins = [
            [
                'name' => 'Manager Parkir',
                'email' => 'manager@parkir.com',
                'phone' => '081234567891',
                'vehicle_number' => 'B5678MGR',
                'password' => Hash::make('manager123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Operator Parkir',
                'email' => 'operator@parkir.com',
                'phone' => '081234567892',
                'vehicle_number' => 'B9012OPT',
                'password' => Hash::make('operator123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        ];

        foreach ($admins as $admin) {
            if (!User::where('email', $admin['email'])->exists()) {
                User::create($admin);
                $this->command->info("Admin {$admin['name']} created successfully!");
            }
        }

        // Create Sample Regular Users untuk testing
        $users = [
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'phone' => '081234567893',
                'vehicle_number' => 'B3456XYZ',
                'password' => Hash::make('user123'),
                'role' => 'user',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'phone' => '081234567894',
                'vehicle_number' => 'B7890ABC',
                'password' => Hash::make('user123'),
                'role' => 'user',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Ahmad Budiman',
                'email' => 'ahmad@example.com',
                'phone' => '081234567895',
                'vehicle_number' => 'B1122DEF',
                'password' => Hash::make('user123'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        ];

        foreach ($users as $user) {
            if (!User::where('email', $user['email'])->exists()) {
                User::create($user);
                $this->command->info("User {$user['name']} created successfully!");
            }
        }

        $this->command->info('All admin and user accounts created successfully!');
    }
}