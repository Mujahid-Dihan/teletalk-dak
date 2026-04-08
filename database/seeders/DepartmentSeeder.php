<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            'System Operations',
            'IT & Billing',
            'P&I',
            'Digital Services',
            'Sales & Marketing',
            'Marketing & VAS',
            'Corporate Sales',
            'Customer Care',
            'Admin & Procurement',
            'Regulatory & Corporate Relation',
            'Legal Affairs',
            'Finance & Accounts',
            '5G Readiness Project',
            'Coastal & Hill Tracts Project'
        ];

        foreach ($departments as $deptName) {
            Department::firstOrCreate(['name' => $deptName]);
        }
    }
}
