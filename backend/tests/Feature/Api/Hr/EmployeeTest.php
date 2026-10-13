<?php

use App\Models\Department;
use App\Models\Designation;
use App\Models\District;
use App\Models\Division;
use App\Models\Employee;
use App\Models\EmployeeFile;
use App\Models\EmployeeWork;
use App\Models\SalaryDetail;
use App\Models\Upazila;
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
        'department_id' => Department::firstOrCreate(['name' => 'Accounts'])->id,
        'designation_id' => Designation::firstOrCreate(['name' => 'Officer'])->id,
        'employment_type' => 'Permanent',
        'status' => 'Active',
        'detail' => [
            'national_id' => '1990123456',
            'payment_method' => 'Bank',
            'emergency_contact_name' => 'Karim',
            'emergency_contact_phone' => '01811000000',
        ],
        'bank' => [
            'bank_name' => 'Demo Bank',
            'account_no' => '0012345',
        ],
    ], $overrides);
}

beforeEach(function () {
    $this->actor = adminUser();
});

it('creates an employee with a sequential code, its detail row and its bank row', function () {
    $this->actingAs($this->actor, 'sanctum')
        ->postJson('/api/hr/employees', employeePayload())
        ->assertStatus(201)
        ->assertJsonPath('data.employee_code', 'EMP-1001')
        ->assertJsonPath('data.department', 'Accounts')
        ->assertJsonPath('data.designation', 'Officer')
        ->assertJsonPath('data.detail.payment_method', 'Bank')
        ->assertJsonPath('data.bank.bank_name', 'Demo Bank');

    $this->assertDatabaseHas('emp_bank_details', ['bank_name' => 'Demo Bank', 'account_no' => '0012345']);

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
            'bank' => [],
        ]))
        ->assertStatus(422)
        ->assertJsonStructure(['data' => ['last_working_date', 'bank.bank_name', 'bank.account_no']]);
});

it('rejects an unknown department or designation', function () {
    $this->actingAs($this->actor, 'sanctum')
        ->postJson('/api/hr/employees', employeePayload(['department_id' => 999, 'designation_id' => 999]))
        ->assertStatus(422)
        ->assertJsonStructure(['data' => ['department_id', 'designation_id']]);
});

it('saves the present and permanent address division, district and thana', function () {
    $division = Division::create(['name' => 'Dhaka']);
    $district = District::create(['division_id' => $division->id, 'name' => 'Gazipur']);
    $upazila = Upazila::create(['district_id' => $district->id, 'name' => 'Tongi']);

    $this->actingAs($this->actor, 'sanctum')
        ->postJson('/api/hr/employees', employeePayload([
            'division_id' => $division->id,
            'district_id' => $district->id,
            'upazila_id' => $upazila->id,
            'detail' => [
                'payment_method' => 'Cash',
                'permanent_division_id' => $division->id,
                'permanent_district_id' => $district->id,
                'permanent_upazila_id' => $upazila->id,
            ],
        ]))
        ->assertStatus(201)
        ->assertJsonPath('data.division', 'Dhaka')
        ->assertJsonPath('data.district', 'Gazipur')
        ->assertJsonPath('data.upazila', 'Tongi')
        ->assertJsonPath('data.detail.permanent_upazila', 'Tongi');
});

it('rejects a district that is not in the chosen division', function () {
    $dhaka = Division::create(['name' => 'Dhaka']);
    $chattogram = Division::create(['name' => 'Chattogram']);
    $district = District::create(['division_id' => $chattogram->id, 'name' => 'Cumilla']);

    $this->actingAs($this->actor, 'sanctum')
        ->postJson('/api/hr/employees', employeePayload(['division_id' => $dhaka->id, 'district_id' => $district->id]))
        ->assertStatus(422)
        ->assertJsonStructure(['data' => ['district_id']]);
});

it('filters the employee list by department and status', function () {
    Employee::factory()->inDepartment('Accounts')->create(['status' => 'Active']);
    Employee::factory()->inDepartment('Accounts')->create(['status' => 'Inactive']);
    Employee::factory()->inDepartment('Sales')->create(['status' => 'Active']);

    $this->actingAs($this->actor, 'sanctum')
        ->getJson('/api/hr/employees?department=Accounts&status=Active')
        ->assertOk()
        ->assertJsonCount(1, 'data.employees');
});

it('updates employee, detail and bank fields', function () {
    $employee = Employee::factory()->create();
    $manager = Designation::factory()->create(['name' => 'Head of Finance']);

    $this->actingAs($this->actor, 'sanctum')
        ->putJson("/api/hr/employees/{$employee->id}", [
            'designation_id' => $manager->id,
            'detail' => ['blood_group' => 'O+'],
            'bank' => ['bank_name' => 'City Bank', 'account_no' => '998877'],
        ])
        ->assertOk()
        ->assertJsonPath('data.designation', 'Head of Finance')
        ->assertJsonPath('data.detail.blood_group', 'O+')
        ->assertJsonPath('data.bank.account_no', '998877');
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
