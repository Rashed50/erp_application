<?php

use App\Models\AccountTransaction;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\EmployeeAdvance;
use App\Models\EmployeeWork;
use App\Models\SalaryDetail;
use App\Models\SalaryHistory;
use App\Models\SalaryPayment;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;

/**
 * An active employee with the spec's 44,000 salary and a January 2026 work record.
 */
function payrollEmployee(array $attributes = [], array $work = []): Employee
{
    // Department and designation are given by name and resolved to their lookup rows.
    foreach (['department' => Department::class, 'designation' => Designation::class] as $key => $model) {
        if (array_key_exists($key, $attributes)) {
            $attributes["{$key}_id"] = $model::firstOrCreate(['name' => $attributes[$key]])->id;
            unset($attributes[$key]);
        }
    }

    $employee = Employee::factory()->create(['joining_date' => '2025-01-01', ...$attributes]);
    SalaryDetail::factory()->for($employee)->create(['effective_date' => '2025-01-01']);
    EmployeeWork::factory()->for($employee)->create(['salary_month' => '2026-01-01', ...$work]);

    return $employee;
}

beforeEach(function () {
    $this->actor = adminUser();
});

describe('preview', function () {
    it('calculates without saving and flags employees who cannot be generated', function () {
        $ready = payrollEmployee();
        $noWork = Employee::factory()->create(['joining_date' => '2025-01-01']);
        SalaryDetail::factory()->for($noWork)->create();

        $response = $this->actingAs($this->actor, 'sanctum')
            ->getJson('/api/hr/payroll/preview?month=2026-01')
            ->assertOk()
            ->assertJsonPath('data.totals.employees', 2)
            ->assertJsonPath('data.totals.generatable', 1)
            ->assertJsonPath('data.totals.net_salary', 44000);

        $rows = collect($response->json('data.rows'))->keyBy('employee_id');
        expect($rows[$ready->id]['can_generate'])->toBeTrue()
            ->and($rows[$noWork->id]['can_generate'])->toBeFalse()
            ->and($rows[$noWork->id]['issues'])->toContain('No work record for this month.')
            ->and(SalaryHistory::count())->toBe(0);
    });

    it('previews a single employee when employee_id is given', function () {
        $employee = payrollEmployee();
        payrollEmployee();

        $this->actingAs($this->actor, 'sanctum')
            ->getJson("/api/hr/payroll/preview?month=2026-01&employee_id={$employee->id}")
            ->assertOk()
            ->assertJsonPath('data.totals.employees', 1)
            ->assertJsonPath('data.rows.0.employee_id', $employee->id);
    });

    it('includes only employees employed during the month', function () {
        $active = payrollEmployee();
        $leftDuringMonth = payrollEmployee(['status' => 'Resigned', 'last_working_date' => '2026-01-20']);
        Employee::factory()->create(['joining_date' => '2026-02-01']);
        Employee::factory()->resigned('2025-12-31')->create(['joining_date' => '2025-01-01']);
        Employee::factory()->create(['joining_date' => '2025-01-01', 'status' => 'Inactive']);

        $ids = collect($this->actingAs($this->actor, 'sanctum')
            ->getJson('/api/hr/payroll/preview?month=2026-01')
            ->json('data.rows'))->pluck('employee_id')->all();

        expect($ids)->toEqualCanonicalizing([$active->id, $leftDuringMonth->id]);
    });
});

describe('generate', function () {
    it('stores a snapshot of the calculation for every generatable employee', function () {
        $employee = payrollEmployee(['department' => 'Accounts', 'designation' => 'Officer']);
        Employee::factory()->create(['joining_date' => '2025-01-01']);

        $this->actingAs($this->actor, 'sanctum')
            ->postJson('/api/hr/payroll/generate', ['month' => '2026-01'])
            ->assertOk()
            ->assertJsonPath('data.created', 1)
            ->assertJsonCount(1, 'data.skipped');

        $salary = SalaryHistory::sole();
        expect($salary->employee_id)->toBe($employee->id)
            ->and($salary->status)->toBe('Generated')
            ->and($salary->employee_code)->toBe($employee->employee_code)
            ->and($salary->department)->toBe('Accounts')
            ->and((float) $salary->gross_salary)->toBe(44000.0)
            ->and((float) $salary->net_salary)->toBe(44000.0)
            ->and($salary->generated_by)->toBe($this->actor->id);
    });

    it('recalculates a generated salary in place instead of creating a duplicate', function () {
        $employee = payrollEmployee();
        $this->actingAs($this->actor, 'sanctum')->postJson('/api/hr/payroll/generate', ['month' => '2026-01']);

        $employee->works()->first()->update(['present_days' => 24, 'absent_days' => 2]);

        $this->actingAs($this->actor, 'sanctum')
            ->postJson('/api/hr/payroll/generate', ['month' => '2026-01'])
            ->assertJsonPath('data.created', 0)
            ->assertJsonPath('data.regenerated', 1);

        expect(SalaryHistory::count())->toBe(1)
            // 30,000 x 24 / 26 working days.
            ->and((float) SalaryHistory::sole()->basic_salary)->toBe(27692.31);
    });

    it('generates only the selected employees', function () {
        $selected = payrollEmployee();
        payrollEmployee();

        $this->actingAs($this->actor, 'sanctum')
            ->postJson('/api/hr/payroll/generate', ['month' => '2026-01', 'employee_ids' => [$selected->id]])
            ->assertJsonPath('data.created', 1);

        expect(SalaryHistory::sole()->employee_id)->toBe($selected->id);
    });

    it('never recalculates an approved salary and keeps history unchanged when salary or employee data changes', function () {
        $employee = payrollEmployee();
        $this->actingAs($this->actor, 'sanctum')->postJson('/api/hr/payroll/generate', ['month' => '2026-01']);
        $salary = SalaryHistory::sole();
        $this->actingAs($this->actor, 'sanctum')->postJson('/api/hr/salary-histories/approve', ['ids' => [$salary->id]]);

        SalaryDetail::factory()->for($employee)->create(['effective_date' => '2026-01-01', 'basic_salary' => 50000]);
        $employee->update(['name' => 'Renamed Person', 'department_id' => Department::firstOrCreate(['name' => 'Sales'])->id]);

        $this->actingAs($this->actor, 'sanctum')
            ->postJson('/api/hr/payroll/generate', ['month' => '2026-01'])
            ->assertJsonPath('data.created', 0)
            ->assertJsonPath('data.regenerated', 0)
            ->assertJsonPath('data.skipped.0.reason', 'Salary already Approved.');

        $salary->refresh();
        expect((float) $salary->basic_salary)->toBe(30000.0)
            ->and($salary->employee_name)->not->toBe('Renamed Person')
            ->and($salary->status)->toBe('Approved');
    });

    it('allows only one live salary per employee and month at the database level', function () {
        $employee = payrollEmployee();
        $this->actingAs($this->actor, 'sanctum')->postJson('/api/hr/payroll/generate', ['month' => '2026-01']);

        $copy = SalaryHistory::sole()->replicate();

        expect(fn () => $copy->save())->toThrow(UniqueConstraintViolationException::class);
    });
});

describe('status workflow', function () {
    beforeEach(function () {
        payrollEmployee();
        $this->actingAs($this->actor, 'sanctum')->postJson('/api/hr/payroll/generate', ['month' => '2026-01']);
        $this->salary = SalaryHistory::sole();
        $this->cashAccountId = cashInHandAccountId();
    });

    it('moves Generated -> Approved -> Paid and records who did it', function () {
        $this->actingAs($this->actor, 'sanctum')
            ->postJson('/api/hr/salary-histories/pay', ['ids' => [$this->salary->id], 'payment_account_id' => $this->cashAccountId, 'payment_date' => '2026-02-01'])
            ->assertJsonPath('data.updated', 0);

        $this->actingAs($this->actor, 'sanctum')
            ->postJson('/api/hr/salary-histories/approve', ['ids' => [$this->salary->id]])
            ->assertJsonPath('data.updated', 1);

        $this->actingAs($this->actor, 'sanctum')
            ->postJson('/api/hr/salary-histories/pay', ['ids' => [$this->salary->id], 'payment_account_id' => $this->cashAccountId, 'payment_date' => '2026-02-01'])
            ->assertJsonPath('data.updated', 1);

        $this->salary->refresh();
        expect($this->salary->status)->toBe('Paid')
            ->and($this->salary->approved_by)->toBe($this->actor->id)
            ->and($this->salary->paid_by)->toBe($this->actor->id);
    });

    it('books an approved salary as Salary Payable at the month end and clears it when paid', function () {
        EmployeeAdvance::factory()->for($this->salary->employee)->create();
        $this->actingAs($this->actor, 'sanctum')->postJson('/api/hr/payroll/generate', ['month' => '2026-01']);
        $this->salary->refresh();
        $this->actingAs($this->actor, 'sanctum')->postJson('/api/hr/salary-histories/approve', ['ids' => [$this->salary->id]]);

        $accrual = AccountTransaction::query()->whereMorphedTo('source', $this->salary)->sole();
        expect($accrual->date->toDateString())->toBe('2026-01-31')
            ->and((float) $accrual->total_amount)->toBe(44000.0)
            ->and(ledgerBalance('5020'))->toBe(44000.0)
            ->and(ledgerBalance('1080'))->toBe(-4000.0)
            ->and(ledgerBalance('2030'))->toBe(-40000.0);

        $this->actingAs($this->actor, 'sanctum')
            ->postJson('/api/hr/salary-histories/pay', ['ids' => [$this->salary->id], 'payment_account_id' => $this->cashAccountId, 'payment_date' => '2026-02-01'])
            ->assertJsonPath('data.updated', 1);

        $payment = SalaryPayment::sole();
        expect((float) $payment->amount)->toBe(40000.0)
            ->and($payment->payment_account_id)->toBe($this->cashAccountId)
            ->and(ledgerBalance('2030'))->toBe(0.0)
            ->and(ledgerBalance('1010'))->toBe(-40000.0)
            ->and(ledgerBalance('5020'))->toBe(44000.0)
            ->and($this->salary->fresh()->status)->toBe('Paid');
    });

    it('pays a salary in parts, refuses more than is due and reopens it when a payment is deleted', function () {
        $this->actingAs($this->actor, 'sanctum')->postJson('/api/hr/salary-histories/approve', ['ids' => [$this->salary->id]]);
        $pay = fn (float $amount) => $this->actingAs($this->actor, 'sanctum')
            ->postJson("/api/hr/salary-histories/{$this->salary->id}/payments", ['amount' => $amount, 'payment_account_id' => $this->cashAccountId, 'payment_date' => '2026-02-05']);

        $pay(30000)->assertCreated()
            ->assertJsonPath('data.status', 'Partially Paid')
            ->assertJsonPath('data.paid_amount', 30000)
            ->assertJsonPath('data.due_amount', 14000)
            ->assertJsonCount(1, 'data.payments');

        $pay(15000)->assertUnprocessable()->assertJsonValidationErrors(['amount'], 'data');

        $this->actingAs($this->actor, 'sanctum')
            ->postJson("/api/hr/salary-histories/{$this->salary->id}/cancel", ['reason' => 'Oops'])
            ->assertUnprocessable();

        $pay(14000)->assertCreated()->assertJsonPath('data.status', 'Paid')->assertJsonPath('data.due_amount', 0);
        expect(ledgerBalance('2030'))->toBe(0.0)->and(ledgerBalance('1010'))->toBe(-44000.0);

        $this->actingAs($this->actor, 'sanctum')
            ->deleteJson('/api/hr/salary-payments/'.SalaryPayment::latest('id')->first()->id)
            ->assertOk()
            ->assertJsonPath('data.status', 'Partially Paid')
            ->assertJsonPath('data.due_amount', 14000);

        expect(ledgerBalance('2030'))->toBe(-14000.0)->and($this->salary->fresh()->paid_by)->toBeNull();
    });

    it('reverses the booked salary when an approved salary is cancelled', function () {
        $this->actingAs($this->actor, 'sanctum')->postJson('/api/hr/salary-histories/approve', ['ids' => [$this->salary->id]]);
        expect(ledgerBalance('5020'))->toBe(44000.0);

        $this->actingAs($this->actor, 'sanctum')
            ->postJson("/api/hr/salary-histories/{$this->salary->id}/cancel", ['reason' => 'Wrong overtime'])
            ->assertOk();

        expect(ledgerBalance('5020'))->toBe(0.0)->and(AccountTransaction::count())->toBe(0);
    });

    it('filters paid and unpaid salaries and totals what is paid and due', function () {
        $other = payrollEmployee();
        $this->actingAs($this->actor, 'sanctum')->postJson('/api/hr/payroll/generate', ['month' => '2026-01']);
        $otherSalary = SalaryHistory::query()->where('employee_id', $other->id)->sole();
        $this->actingAs($this->actor, 'sanctum')->postJson('/api/hr/salary-histories/approve', ['ids' => [$this->salary->id, $otherSalary->id]]);
        $this->actingAs($this->actor, 'sanctum')->postJson('/api/hr/salary-histories/pay', ['ids' => [$this->salary->id], 'payment_account_id' => $this->cashAccountId, 'payment_date' => '2026-02-01']);

        $this->actingAs($this->actor, 'sanctum')
            ->getJson('/api/hr/salary-histories?month=2026-01')
            ->assertJsonPath('data.totals.paid_amount', 44000)
            ->assertJsonPath('data.totals.due_amount', 44000);

        $this->actingAs($this->actor, 'sanctum')
            ->getJson('/api/hr/salary-histories?month=2026-01&payment_status=unpaid')
            ->assertJsonCount(1, 'data.salaries')
            ->assertJsonPath('data.salaries.0.id', $otherSalary->id);

        $this->actingAs($this->actor, 'sanctum')
            ->getJson('/api/hr/salary-histories?month=2026-01&payment_status=paid')
            ->assertJsonCount(1, 'data.salaries')
            ->assertJsonPath('data.salaries.0.id', $this->salary->id);

        $this->actingAs($this->actor, 'sanctum')
            ->getJson('/api/hr/reports/salary-due')
            ->assertOk()
            ->assertJsonCount(1, 'data.rows')
            ->assertJsonPath('data.rows.0.id', $otherSalary->id)
            ->assertJsonPath('data.employees.0.due_amount', 44000)
            ->assertJsonPath('data.totals.due_amount', 44000);
    });

    it('requires a cash/bank account and date to pay salaries', function () {
        $this->actingAs($this->actor, 'sanctum')
            ->postJson('/api/hr/salary-histories/pay', ['ids' => [$this->salary->id]])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['payment_account_id', 'payment_date'], 'data');
    });

    it('lets an approved salary be cancelled and generated again, keeping the cancelled record', function () {
        $this->actingAs($this->actor, 'sanctum')->postJson('/api/hr/salary-histories/approve', ['ids' => [$this->salary->id]]);

        $this->actingAs($this->actor, 'sanctum')
            ->postJson("/api/hr/salary-histories/{$this->salary->id}/cancel", ['reason' => 'Wrong overtime'])
            ->assertOk()
            ->assertJsonPath('data.status', 'Cancelled');

        $this->actingAs($this->actor, 'sanctum')
            ->postJson('/api/hr/payroll/generate', ['month' => '2026-01'])
            ->assertJsonPath('data.created', 1);

        expect(SalaryHistory::count())->toBe(2)
            ->and(SalaryHistory::live()->sole()->status)->toBe('Generated');
    });

    it('refuses to cancel a paid salary', function () {
        $this->actingAs($this->actor, 'sanctum')->postJson('/api/hr/salary-histories/approve', ['ids' => [$this->salary->id]]);
        $this->actingAs($this->actor, 'sanctum')->postJson('/api/hr/salary-histories/pay', ['ids' => [$this->salary->id], 'payment_account_id' => $this->cashAccountId, 'payment_date' => '2026-02-01']);

        $this->actingAs($this->actor, 'sanctum')
            ->postJson("/api/hr/salary-histories/{$this->salary->id}/cancel", ['reason' => 'Oops'])
            ->assertStatus(422);
    });

    it('lists salaries with totals and hides cancelled ones by default', function () {
        $this->actingAs($this->actor, 'sanctum')
            ->postJson("/api/hr/salary-histories/{$this->salary->id}/cancel", ['reason' => 'Test']);

        $this->actingAs($this->actor, 'sanctum')
            ->getJson('/api/hr/salary-histories?month=2026-01')
            ->assertOk()
            ->assertJsonCount(0, 'data.salaries');

        $this->actingAs($this->actor, 'sanctum')
            ->getJson('/api/hr/salary-histories?month=2026-01&include_cancelled=1')
            ->assertJsonCount(1, 'data.salaries')
            ->assertJsonPath('data.totals.net_salary', 44000);
    });
});

describe('work records', function () {
    it('rejects a second work record for the same employee and month', function () {
        $employee = payrollEmployee();

        $this->actingAs($this->actor, 'sanctum')
            ->postJson('/api/hr/employee-works', [
                'employee_id' => $employee->id, 'salary_month' => '2026-01',
                'working_days' => 26, 'present_days' => 26, 'absent_days' => 0, 'paid_leave_days' => 0,
                'unpaid_leave_days' => 0, 'overtime_hours' => 0, 'bonus' => 0, 'other_addition' => 0, 'other_deduction' => 0,
            ])
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['salary_month']]);
    });

    it('rejects day counts that exceed the working days or the month', function () {
        $employee = Employee::factory()->create(['joining_date' => '2025-01-01']);
        $payload = [
            'employee_id' => $employee->id, 'salary_month' => '2026-02',
            'working_days' => 20, 'present_days' => 18, 'absent_days' => 2, 'paid_leave_days' => 1,
            'unpaid_leave_days' => 0, 'overtime_hours' => 0, 'bonus' => 0, 'other_addition' => 0, 'other_deduction' => 0,
        ];

        $this->actingAs($this->actor, 'sanctum')
            ->postJson('/api/hr/employee-works', $payload)
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['present_days']]);

        $this->actingAs($this->actor, 'sanctum')
            ->postJson('/api/hr/employee-works', [...$payload, 'working_days' => 30, 'present_days' => 30, 'absent_days' => 0, 'paid_leave_days' => 0])
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['working_days']]);
    });

    it('rejects a month in which the employee was not employed', function () {
        $employee = Employee::factory()->create(['joining_date' => '2026-03-01']);

        $this->actingAs($this->actor, 'sanctum')
            ->postJson('/api/hr/employee-works', [
                'employee_id' => $employee->id, 'salary_month' => '2026-02',
                'working_days' => 20, 'present_days' => 20, 'absent_days' => 0, 'paid_leave_days' => 0,
                'unpaid_leave_days' => 0, 'overtime_hours' => 0, 'bonus' => 0, 'other_addition' => 0, 'other_deduction' => 0,
            ])
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['salary_month']]);
    });

    it('locks the work record once its salary is approved', function () {
        $employee = payrollEmployee();
        $work = $employee->works()->first();
        $this->actingAs($this->actor, 'sanctum')->postJson('/api/hr/payroll/generate', ['month' => '2026-01']);
        $this->actingAs($this->actor, 'sanctum')->postJson('/api/hr/salary-histories/approve', ['ids' => [SalaryHistory::sole()->id]]);

        $this->actingAs($this->actor, 'sanctum')
            ->getJson("/api/hr/employee-works/find?employee_id={$employee->id}&month=2026-01")
            ->assertJsonPath('data.is_locked', true);

        $this->actingAs($this->actor, 'sanctum')
            ->putJson("/api/hr/employee-works/{$work->id}", [
                'working_days' => 26, 'present_days' => 25, 'absent_days' => 1, 'paid_leave_days' => 0,
                'unpaid_leave_days' => 0, 'overtime_hours' => 0, 'bonus' => 0, 'other_addition' => 0, 'other_deduction' => 0,
            ])
            ->assertStatus(422);
    });
});

describe('salary configuration', function () {
    it('cannot edit or delete a revision already used by payroll', function () {
        $employee = payrollEmployee();
        $this->actingAs($this->actor, 'sanctum')->postJson('/api/hr/payroll/generate', ['month' => '2026-01']);
        $config = $employee->salaryDetails()->first();

        $this->actingAs($this->actor, 'sanctum')
            ->putJson("/api/hr/salary-details/{$config->id}", [...$config->only([
                'house_rent', 'medical_allowance', 'transport_allowance', 'food_allowance', 'other_allowance',
                'overtime_rate', 'other_deduction', 'deduction_basis', 'status',
            ]), 'effective_date' => '2025-01-01', 'basic_salary' => 99000])
            ->assertStatus(422);

        $this->actingAs($this->actor, 'sanctum')->deleteJson("/api/hr/salary-details/{$config->id}")->assertStatus(422);
    });

    it('adds a new revision with a later effective date', function () {
        $employee = payrollEmployee();

        $this->actingAs($this->actor, 'sanctum')
            ->postJson("/api/hr/employees/{$employee->id}/salary-details", [
                'effective_date' => '2026-02-01', 'basic_salary' => 35000, 'house_rent' => 10000,
                'medical_allowance' => 2000, 'transport_allowance' => 2000, 'food_allowance' => 0, 'other_allowance' => 0,
                'overtime_rate' => 0, 'other_deduction' => 0, 'deduction_basis' => 'basic', 'status' => true,
            ])
            ->assertStatus(201)
            ->assertJsonPath('data.monthly_gross', 49000);

        expect($employee->salaryDetails()->count())->toBe(2);
    });
});

describe('permissions', function () {
    it('forbids payroll actions without the permission', function () {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')->getJson('/api/hr/payroll/preview?month=2026-01')->assertForbidden();
        $this->actingAs($user, 'sanctum')->postJson('/api/hr/payroll/generate', ['month' => '2026-01'])->assertForbidden();
        $this->actingAs($user, 'sanctum')->getJson('/api/hr/employees')->assertForbidden();
    });
});

describe('reports', function () {
    it('returns dashboard figures and department-wise and yearly summaries', function () {
        payrollEmployee(['department' => 'Accounts']);
        payrollEmployee(['department' => 'Sales']);
        $this->actingAs($this->actor, 'sanctum')->postJson('/api/hr/payroll/generate', ['month' => '2026-01']);

        $this->actingAs($this->actor, 'sanctum')
            ->getJson('/api/hr/dashboard')
            ->assertOk()
            ->assertJsonPath('data.total_employees', 2)
            ->assertJsonPath('data.active_employees', 2);

        $this->actingAs($this->actor, 'sanctum')
            ->getJson('/api/hr/reports/department-wise?month=2026-01')
            ->assertJsonCount(2, 'data.rows')
            ->assertJsonPath('data.rows.0.department', 'Accounts')
            ->assertJsonPath('data.rows.0.net_salary', 44000);

        $this->actingAs($this->actor, 'sanctum')
            ->getJson('/api/hr/reports/salary-summary?year=2026')
            ->assertJsonPath('data.rows.0.month', '2026-01')
            ->assertJsonPath('data.rows.0.employees', 2)
            ->assertJsonPath('data.rows.0.unpaid_amount', 88000);
    });
});
