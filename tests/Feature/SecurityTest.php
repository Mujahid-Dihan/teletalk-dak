<?php

use App\Models\User;
use App\Models\Department;
use App\Models\DakFile;

beforeEach(function () {
    $this->salesDept = Department::create(['name' => 'Sales & Marketing']);
    $this->hrDept = Department::create(['name' => 'Admin & Procurement']);

    // Standard Officer in Sales
    $this->salesOfficer = User::factory()->create([
        'role' => 'officer',
        'department_id' => $this->salesDept->id,
    ]);
});

test('an officer cannot archive a file that is not in their department', function () {
    // 1. Create a file currently sitting in HR
    $file = DakFile::create([
        'tracking_id' => 'TTBL-SEC-001',
        'subject' => 'Confidential HR Record',
        'priority' => 'Urgent',
        'status' => 'Pending',
        'origin_department_id' => $this->hrDept->id,
        'current_department_id' => $this->hrDept->id, // Currently at HR
    ]);

    // 2. The Sales Officer attempts to maliciously archive the HR file
    $response = $this->actingAs($this->salesOfficer)
                     ->patch(route('dak.archive', $file->id), [
                         'physical_location' => 'Stolen Cabinet'
                     ]);

    // 3. We expect the system to throw a 403 Forbidden error
    $response->assertForbidden(); 
    
    // 4. Verify the database was NOT altered
    $this->assertDatabaseMissing('dak_files', [
        'id' => $file->id,
        'status' => 'Completed'
    ]);
});
