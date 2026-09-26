<?php

use App\Models\Employee;
use App\Models\EmployeeFile;
use App\Models\EmployeeWork;
use App\Models\SalaryDetail;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function employeePayload(array $overrides = []): array
{
    return array_merge([
        'name' => 'Rahim Uddin',
        'gender' => 'Male',
        'phone' => '01711000000',
        'email' => 'rahim@example.com',
        'joining_date' => '2025-03-01',
        'department' => 'Accounts',
        'designation' => 'Officer',
        'employment_type' => 'Permanent',
        'status' => 'Active',
        'detail' => [
            'national_id' => '1990123456',
            'payment_method' => 'Bank',
            'bank_name' => 'Demo Bank',
            'bank_account_no' => '0012345',
            'emergency_contact_name' => 'Karim',
            'emergency_contact_phone' => '01811000000',
        ],
    ], $overrides);
}

beforeEach(function () {
    $this->actor = adminUser();
});

it('creates an employee with a sequential code and its detail row', function () {
    $this->actingAs($this->actor, 'sanctum')
        ->postJson('/api/hr/employees', employeePayload())
        ->assertStatus(201)
        ->assertJsonPath('data.employee_code', 'EMP-1001')
        ->assertJsonPath('data.detail.bank_name', 'Demo Bank');

    $this->actingAs($this->actor, 'sanctum')
        ->postJson('/api/hr/employees', employeePayload(['email' => 'second@example.com']))
        ->assertJsonPath('data.employee_code', 'EMP-1002');
});

it('rejects a duplicate employee code', function () {
    Employee::factory()->create(['employee_code' => 'EMP-5000']);

    $this->actingAs($this->actor, 'sanctum')
        ->postJson('/api/hr/employees', employeePayload(['employee_code' => 'EMP-5000']))
        ->assertStatus(422)
        ->assertJsonStructure(['data' => ['employee_code']]);
});

it('requires bank details for bank payment and a last working date when resigned', function () {
    $this->actingAs($this->actor, 'sanctum')
        ->postJson('/api/hr/employees', employeePayload([
            'status' => 'Resigned',
            'detail' => ['payment_method' => 'Bank'],
        ]))
        ->assertStatus(422)
        ->assertJsonStructure(['data' => ['last_working_date', 'detail.bank_name', 'detail.bank_account_no']]);
});

it('filters the employee list by department and status', function () {
    Employee::factory()->create(['department' => 'Accounts', 'status' => 'Active']);
    Employee::factory()->create(['department' => 'Accounts', 'status' => 'Inactive']);
    Employee::factory()->create(['department' => 'Sales', 'status' => 'Active']);

    $this->actingAs($this->actor, 'sanctum')
        ->getJson('/api/hr/employees?department=Accounts&status=Active')
        ->assertOk()
        ->assertJsonCount(1, 'data.employees');
});

it('updates employee and detail fields', function () {
    $employee = Employee::factory()->create();

    $this->actingAs($this->actor, 'sanctum')
        ->putJson("/api/hr/employees/{$employee->id}", ['designation' => 'Manager', 'detail' => ['blood_group' => 'O+']])
        ->assertOk()
        ->assertJsonPath('data.designation', 'Manager')
        ->assertJsonPath('data.detail.blood_group', 'O+');
});

it('blocks deleting an employee with salary history but soft deletes one without', function () {
    $employee = Employee::factory()->create(['joining_date' => '2025-01-01']);
    SalaryDetail::factory()->for($employee)->create();
    EmployeeWork::factory()->for($employee)->create();
    $this->actingAs($this->actor, 'sanctum')->postJson('/api/hr/payroll/generate', ['month' => '2026-01']);

    $this->actingAs($this->actor, 'sanctum')->deleteJson("/api/hr/employees/{$employee->id}")->assertStatus(422);

    $fresh = Employee::factory()->create();
    $this->actingAs($this->actor, 'sanctum')->deleteJson("/api/hr/employees/{$fresh->id}")->assertOk();
    $this->assertSoftDeleted('employee_info', ['id' => $fresh->id, 'deleted_by' => $this->actor->id]);
});

it('uploads, downloads and deletes documents on the private disk', function () {
    Storage::fake(EmployeeFile::DISK);
    $employee = Employee::factory()->create();

    $fileId = $this->actingAs($this->actor, 'sanctum')
        ->post("/api/hr/employees/{$employee->id}/files", [
            'document_type' => 'NID',
            'title' => 'National ID',
            'file' => UploadedFile::fake()->create('nid.pdf', 100, 'application/pdf'),
        ], ['Accept' => 'application/json'])
        ->assertStatus(201)
        ->assertJsonMissingPath('data.file_path')
        ->json('data.id');

    $file = EmployeeFile::find($fileId);
    Storage::disk(EmployeeFile::DISK)->assertExists($file->file_path);

    $this->actingAs($this->actor, 'sanctum')
        ->get("/api/hr/employee-files/{$fileId}/download")
        ->assertOk()
        ->assertDownload('nid.pdf');

    $this->actingAs($this->actor, 'sanctum')->deleteJson("/api/hr/employee-files/{$fileId}")->assertOk();
    Storage::disk(EmployeeFile::DISK)->assertMissing($file->file_path);
});

it('rejects a document of a disallowed type', function () {
    Storage::fake(EmployeeFile::DISK);
    $employee = Employee::factory()->create();

    $this->actingAs($this->actor, 'sanctum')
        ->post("/api/hr/employees/{$employee->id}/files", [
            'document_type' => 'CV',
            'file' => UploadedFile::fake()->create('script.exe', 10),
        ], ['Accept' => 'application/json'])
        ->assertStatus(422);
});
