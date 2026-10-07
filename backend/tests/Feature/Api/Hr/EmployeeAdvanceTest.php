<?php

use App\Models\Employee;
use App\Models\EmployeeAdvance;
use App\Models\EmployeeAdvanceRecovery;
use App\Models\EmployeeWork;
use App\Models\SalaryDetail;
use App\Models\SalaryHistory;
use App\Models\User;

/**
 * An employee with the 44,000 salary and work records for January to March 2026.
 */
function advanceEmployee(): Employee
{
    $employee = Employee::factory()->create(['joining_date' => '2025-01-01']);
    SalaryDetail::factory()->for($employee)->create(['effective_date' => '2025-01-01']);

    foreach (['2026-01-01', '2026-02-01', '2026-03-01'] as $month) {
        EmployeeWork::factory()->for($employee)->create(['salary_month' => $month]);
    }

    return $employee;
}

beforeEach(function () {
    $this->actor = adminUser();
});

describe('advance entry', function () {
    it('saves an advance with the installment defaulting to amount / installments', function () {
        $employee = Employee::factory()->create();

        $this->actingAs($this->actor, 'sanctum')
            ->postJson('/api/hr/employee-advances', [
                'employee_id' => $employee->id,
                'advance_date' => '2026-01-10',
                'amount' => 10000,
                'installment_count' => 3,
                'deduction_start_month' => '2026-01',
                'purpose' => 'Medical',
            ])
            ->assertCreated()
            ->assertJsonPath('data.installment_amount', 3333.33)
            ->assertJsonPath('data.outstanding_amount', 10000)
            ->assertJsonPath('data.status', 'Running');

        expect(EmployeeAdvance::sole()->created_by)->toBe($this->actor->id);
    });

    it('rejects a deduction start before the advance month and an installment above the amount', function () {
        $employee = Employee::factory()->create();

        $this->actingAs($this->actor, 'sanctum')
            ->postJson('/api/hr/employee-advances', [
                'employee_id' => $employee->id,
                'advance_date' => '2026-02-10',
                'amount' => 1000,
                'installment_count' => 1,
                'installment_amount' => 1500,
                'deduction_start_month' => '2026-01',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['installment_amount'], 'data');

        $this->actingAs($this->actor, 'sanctum')
            ->postJson('/api/hr/employee-advances', [
                'employee_id' => $employee->id,
                'advance_date' => '2026-02-10',
                'amount' => 1000,
                'installment_count' => 1,
                'deduction_start_month' => '2026-01',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['deduction_start_month'], 'data');
    });

    it('requires the advance permissions', function () {
        $advance = EmployeeAdvance::factory()->create();

        $this->actingAs(User::factory()->create(), 'sanctum')
            ->getJson('/api/hr/employee-advances')
            ->assertForbidden();

        $this->actingAs(User::factory()->create(), 'sanctum')
            ->deleteJson("/api/hr/employee-advances/{$advance->id}")
            ->assertForbidden();
    });

    it('lists advances with totals', function () {
        EmployeeAdvance::factory()->create(['amount' => 12000, 'recovered_amount' => 4000]);
        EmployeeAdvance::factory()->create(['amount' => 6000, 'recovered_amount' => 6000, 'status' => 'Completed']);

        $this->actingAs($this->actor, 'sanctum')
            ->getJson('/api/hr/employee-advances?status=Running')
            ->assertOk()
            ->assertJsonCount(1, 'data.advances')
            ->assertJsonPath('data.totals.outstanding_amount', 8000);
    });
});

describe('salary deduction', function () {
    it('deducts the installment from each month until the advance is recovered', function () {
        $employee = advanceEmployee();
        $advance = EmployeeAdvance::factory()->for($employee)->create([
            'amount' => 10000, 'installment_count' => 3, 'installment_amount' => 4000,
        ]);

        foreach (['2026-01', '2026-02', '2026-03'] as $month) {
            $this->actingAs($this->actor, 'sanctum')->postJson('/api/hr/payroll/generate', ['month' => $month])->assertOk();
        }

        $salaries = SalaryHistory::query()->orderBy('salary_month')->get();
        expect($salaries->pluck('advance_deduction')->map(fn ($amount) => (float) $amount)->all())->toBe([4000.0, 4000.0, 2000.0])
            ->and((float) $salaries[0]->net_salary)->toBe(40000.0)
            ->and((float) $salaries[0]->total_deduction)->toBe(4000.0)
            ->and($advance->fresh()->status)->toBe('Completed')
            ->and((float) $advance->fresh()->recovered_amount)->toBe(10000.0)
            ->and(EmployeeAdvanceRecovery::where('type', 'Salary')->count())->toBe(3);
    });

    it('shows the installment in the preview', function () {
        $employee = advanceEmployee();
        EmployeeAdvance::factory()->for($employee)->create();

        $this->actingAs($this->actor, 'sanctum')
            ->getJson('/api/hr/payroll/preview?month=2026-01')
            ->assertJsonPath('data.rows.0.calculation.advance_deduction', 4000)
            ->assertJsonPath('data.rows.0.calculation.net_salary', 40000);
    });

    it('does not deduct before the deduction start month', function () {
        $employee = advanceEmployee();
        EmployeeAdvance::factory()->for($employee)->create(['deduction_start_month' => '2026-02-01']);

        $this->actingAs($this->actor, 'sanctum')->postJson('/api/hr/payroll/generate', ['month' => '2026-01']);

        expect((float) SalaryHistory::sole()->advance_deduction)->toBe(0.0);
    });

    it('recalculates the same installment when a month is generated again', function () {
        $employee = advanceEmployee();
        $advance = EmployeeAdvance::factory()->for($employee)->create();

        $this->actingAs($this->actor, 'sanctum')->postJson('/api/hr/payroll/generate', ['month' => '2026-01']);
        $this->actingAs($this->actor, 'sanctum')->postJson('/api/hr/payroll/generate', ['month' => '2026-01'])
            ->assertJsonPath('data.regenerated', 1);

        expect((float) SalaryHistory::sole()->advance_deduction)->toBe(4000.0)
            ->and(EmployeeAdvanceRecovery::count())->toBe(1)
            ->and((float) $advance->fresh()->recovered_amount)->toBe(4000.0);
    });

    it('gives the installment back when the salary is cancelled', function () {
        $employee = advanceEmployee();
        $advance = EmployeeAdvance::factory()->for($employee)->create();
        $this->actingAs($this->actor, 'sanctum')->postJson('/api/hr/payroll/generate', ['month' => '2026-01']);

        $this->actingAs($this->actor, 'sanctum')
            ->postJson('/api/hr/salary-histories/'.SalaryHistory::sole()->id.'/cancel', ['reason' => 'Wrong attendance'])
            ->assertOk();

        expect(EmployeeAdvanceRecovery::count())->toBe(0)
            ->and((float) $advance->fresh()->recovered_amount)->toBe(0.0);
    });

    it('deducts only what the salary can cover and carries the rest forward', function () {
        $employee = advanceEmployee();
        $advance = EmployeeAdvance::factory()->for($employee)->create([
            'amount' => 100000, 'installment_count' => 1, 'installment_amount' => 100000,
        ]);

        $this->actingAs($this->actor, 'sanctum')->postJson('/api/hr/payroll/generate', ['month' => '2026-01']);

        $salary = SalaryHistory::sole();
        expect((float) $salary->advance_deduction)->toBe(44000.0)
            ->and((float) $salary->net_salary)->toBe(0.0)
            ->and($advance->fresh()->outstandingAmount())->toBe(56000.0);
    });

    it('recovers several advances oldest first', function () {
        $employee = advanceEmployee();
        $older = EmployeeAdvance::factory()->for($employee)->create(['advance_date' => '2025-11-01', 'amount' => 3000, 'installment_amount' => 3000]);
        $newer = EmployeeAdvance::factory()->for($employee)->create(['advance_date' => '2025-12-01', 'amount' => 5000, 'installment_amount' => 2500]);

        $this->actingAs($this->actor, 'sanctum')->postJson('/api/hr/payroll/generate', ['month' => '2026-01']);

        expect((float) SalaryHistory::sole()->advance_deduction)->toBe(5500.0)
            ->and($older->fresh()->status)->toBe('Completed')
            ->and((float) $newer->fresh()->recovered_amount)->toBe(2500.0);
    });
});

describe('cash repayment', function () {
    it('records cash repayment and lowers the next installment to the balance', function () {
        $employee = advanceEmployee();
        $advance = EmployeeAdvance::factory()->for($employee)->create();

        $this->actingAs($this->actor, 'sanctum')
            ->postJson("/api/hr/employee-advances/{$advance->id}/recoveries", ['amount' => 10000, 'recovery_date' => '2026-01-05'])
            ->assertCreated()
            ->assertJsonPath('data.outstanding_amount', 2000);

        $this->actingAs($this->actor, 'sanctum')->postJson('/api/hr/payroll/generate', ['month' => '2026-01']);

        expect((float) SalaryHistory::sole()->advance_deduction)->toBe(2000.0)
            ->and($advance->fresh()->status)->toBe('Completed');
    });

    it('rejects a repayment above the outstanding balance', function () {
        $advance = EmployeeAdvance::factory()->create(['recovered_amount' => 10000]);

        $this->actingAs($this->actor, 'sanctum')
            ->postJson("/api/hr/employee-advances/{$advance->id}/recoveries", ['amount' => 2500, 'recovery_date' => '2026-01-05'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['amount'], 'data');
    });

    it('deletes a cash repayment but not a salary installment', function () {
        $employee = advanceEmployee();
        $advance = EmployeeAdvance::factory()->for($employee)->create();
        $this->actingAs($this->actor, 'sanctum')->postJson("/api/hr/employee-advances/{$advance->id}/recoveries", ['amount' => 1000, 'recovery_date' => '2026-01-05']);
        $this->actingAs($this->actor, 'sanctum')->postJson('/api/hr/payroll/generate', ['month' => '2026-01']);

        $cash = EmployeeAdvanceRecovery::where('type', 'Cash')->sole();
        $installment = EmployeeAdvanceRecovery::where('type', 'Salary')->sole();

        $this->actingAs($this->actor, 'sanctum')->deleteJson("/api/hr/advance-recoveries/{$installment->id}")->assertUnprocessable();
        $this->actingAs($this->actor, 'sanctum')->deleteJson("/api/hr/advance-recoveries/{$cash->id}")
            ->assertOk()
            ->assertJsonPath('data.recovered_amount', 4000);
    });
});

describe('changing an advance', function () {
    it('cannot delete an advance that has recoveries', function () {
        $employee = advanceEmployee();
        $advance = EmployeeAdvance::factory()->for($employee)->create();
        $this->actingAs($this->actor, 'sanctum')->postJson('/api/hr/payroll/generate', ['month' => '2026-01']);

        $this->actingAs($this->actor, 'sanctum')->deleteJson("/api/hr/employee-advances/{$advance->id}")->assertUnprocessable();

        $fresh = EmployeeAdvance::factory()->create();
        $this->actingAs($this->actor, 'sanctum')->deleteJson("/api/hr/employee-advances/{$fresh->id}")->assertOk();
        expect(EmployeeAdvance::find($fresh->id))->toBeNull();
    });

    it('changes the installment of a running advance but keeps its start month and recovered amount', function () {
        $employee = advanceEmployee();
        $advance = EmployeeAdvance::factory()->for($employee)->create();
        $this->actingAs($this->actor, 'sanctum')->postJson('/api/hr/payroll/generate', ['month' => '2026-01']);

        $payload = ['advance_date' => '2025-12-20', 'amount' => 12000, 'installment_count' => 4, 'installment_amount' => 2000, 'deduction_start_month' => '2026-01'];

        $this->actingAs($this->actor, 'sanctum')
            ->putJson("/api/hr/employee-advances/{$advance->id}", $payload)
            ->assertOk()
            ->assertJsonPath('data.installment_amount', 2000);

        $this->actingAs($this->actor, 'sanctum')
            ->putJson("/api/hr/employee-advances/{$advance->id}", [...$payload, 'deduction_start_month' => '2026-02'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['deduction_start_month'], 'data');

        $this->actingAs($this->actor, 'sanctum')
            ->putJson("/api/hr/employee-advances/{$advance->id}", [...$payload, 'amount' => 3000, 'installment_amount' => 1000])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['amount'], 'data');

        $this->actingAs($this->actor, 'sanctum')->postJson('/api/hr/payroll/generate', ['month' => '2026-02']);
        expect((float) SalaryHistory::query()->forMonth('2026-02')->sole()->advance_deduction)->toBe(2000.0);
    });
});
