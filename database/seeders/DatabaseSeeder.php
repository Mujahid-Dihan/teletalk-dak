<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            DepartmentSeeder::class,
        ]);

        $itDepartment = Department::where('name', 'IT & Billing')->first();
        $legalDepartment = Department::where('name', 'Legal Affairs')->first();

        // 1. Super Admin (IT)
        User::firstOrCreate(
            ['email' => 'admin@teletalk.com.bd'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password123'),
                'role' => 'super_admin',
                'department_id' => $itDepartment->id,
                'is_approved' => true, // <-- এই লাইনটি যুক্ত করুন
            ]
        );

        // 2. Admin (Legal)
        User::firstOrCreate(
            ['email' => 'legal@teletalk.com.bd'],
            [
                'name' => 'Legal Admin',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'department_id' => $legalDepartment->id,
                'is_approved' => true, // <-- এই লাইনটি যুক্ত করুন
            ]
        );
        
        // 3. Staff (IT)
        User::firstOrCreate(
            ['email' => 'staff@teletalk.com.bd'],
            [
                'name' => 'IT Staff',
                'password' => Hash::make('password123'),
                'role' => 'staff',
                'department_id' => $itDepartment->id,
                'is_approved' => true, // <-- এই লাইনটি যুক্ত করুন
            ]
        );
        // 4. Viewer (Legal)
        User::firstOrCreate(
            ['email' => 'viewer@teletalk.com.bd'],
            [
                'name' => 'General Viewer',
                'password' => Hash::make('password123'),
                'role' => 'viewer',
                'department_id' => $legalDepartment->id,
                'is_approved' => true,
            ]
        );
    }
}
