<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin role
        $adminRole = Role::where('name', 'admin')->first();

        // Check if admin role exists
        if (!$adminRole) {
            $this->command->error('Admin role not found! Make sure RolePermissionSeeder has been run first.');
            return;
        }

        // Admin user data
        $adminData = [
            'name' => 'Super Administrator',
            'pangkat' => 'Kolonel',
            'korps' => 'TNI AD',
            'nrp' => '11223344',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'status' => 'aktif',
        ];

        // Check if admin user already exists
        $existingUser = User::where('email', $adminData['email'])
            ->orWhere('nrp', $adminData['nrp'])
            ->first();

        if ($existingUser) {
            $this->command->warn("Admin user with email {$adminData['email']} or NRP {$adminData['nrp']} already exists. Skipping...");
            return;
        }

        // Create admin user
        $user = User::create($adminData);

        // Assign admin role
        $user->assignRole($adminRole);

        $this->command->info("Admin user {$adminData['name']} created successfully.");

        $this->command->info('AdminSeeder completed successfully!');
    }
}
