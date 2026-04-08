<?php

use App\Models\User;
use App\Models\Department;
use App\Models\DakFile;

// Runs before each test to set up our baseline environment
beforeEach(function () {
    $this->itDept = Department::create(['name' => 'IT & Billing']);
    $this->legalDept = Department::create(['name' => 'Legal Affairs']);

    $this->officer = User::factory()->create([
        'role' => 'officer',
        'department_id' => $this->itDept->id,
    ]);
});

test('an officer can initiate and dispatch a new dak file', function () {
    // 1. Act as the logged-in officer and hit the store route
    $this->actingAs($this->officer)
         ->post(route('dak.store'), [
             'tracking_id' => 'TTBL-TEST-001',
             'subject' => 'Server Maintenance Contract',
             'priority' => 'High',
             'current_department_id' => $this->legalDept->id,
         ])
         ->assertSessionHasNoErrors()
         ->assertRedirect();

    // 2. Verify the file exists in the database with correct routing
    $this->assertDatabaseHas('dak_files', [
        'tracking_id' => 'TTBL-TEST-001',
        'origin_department_id' => $this->itDept->id,
        'current_department_id' => $this->legalDept->id,
    ]);

    // 3. Verify the Audit Trail logged the creation
    $this->assertDatabaseHas('file_movements', [
        'user_id' => $this->officer->id,
        'from_department_id' => $this->itDept->id,
        'to_department_id' => $this->legalDept->id,
        'action' => 'Initiated',
    ]);
});

test('an officer can forward an active file to another department', function () {
    // 1. Create an existing file currently sitting in the IT Department
    $file = DakFile::create([
        'tracking_id' => 'TTBL-TEST-002',
        'subject' => 'Software License',
        'priority' => 'Normal',
        'status' => 'Pending',
        'origin_department_id' => $this->legalDept->id,
        'current_department_id' => $this->itDept->id, 
    ]);

    // 2. Officer forwards it back to Legal
    $this->actingAs($this->officer)
         ->post(route('dak.forward', $file->id), [
             'target_department_id' => $this->legalDept->id,
             'remarks' => 'Reviewed and approved by IT.',
         ])
         ->assertRedirect();

    // 3. Verify the file's current location updated
    $this->assertDatabaseHas('dak_files', [
        'id' => $file->id,
        'current_department_id' => $this->legalDept->id,
        'status' => 'In-Transit',
    ]);

    // 4. Verify the Audit Trail logged the forward action and remarks
    $this->assertDatabaseHas('file_movements', [
        'dak_file_id' => $file->id,
        'action' => 'Forwarded',
        'to_department_id' => $this->legalDept->id,
        'remarks' => 'Reviewed and approved by IT.',
    ]);
});
