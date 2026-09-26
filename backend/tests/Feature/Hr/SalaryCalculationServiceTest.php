<?php

use App\Models\Employee;
use App\Models\EmployeeWork;
use App\Models\SalaryDetail;
use App\Services\SalaryCalculationService;
use Carbon\CarbonImmutable;

function calculateSalary(Employee $employee, string $month, array $work = [], ?SalaryDetail $config = null): array
{
    $monthStart = CarbonImmutable::parse($month.'-01');
    $config ??= $employee->salaryDetailFor($monthStart);
    $work = EmployeeWork::factory()->for($employee)->create(['salary_month' => $monthStart->toDateString(), ...$work]);

    return app(SalaryCalculationService::class)->calculate($employee, $monthStart, $config, $work);
}

beforeEach(function () {
    $this->employee = Employee::factory()->create(['joining_date' => '2025-01-01']);
    SalaryDetail::factory()->for($this->employee)->create(['effective_date' => '2025-01-01']);
});

it('adds basic and allowances for a full month with perfect attendance', function () {
    $salary = calculateSalary($this->employee, '2026-01');

    expect($salary['basic_salary'])->toBe(30000.0)
        ->and($salary['total_allowance'])->toBe(14000.0)
        ->and($salary['gross_salary'])->toBe(44000.0)
        ->and($salary['total_deduction'])->toBe(0.0)
        ->and($salary['net_salary'])->toBe(44000.0)
        ->and($salary['employed_days'])->toBe(31);
});

it('deducts absence and unpaid leave at basic / working days', function () {
    $salary = calculateSalary($this->employee, '2026-01', [
        'present_days' => 23, 'absent_days' => 2, 'unpaid_leave_days' => 1,
    ]);

    // 30,000 / 26 = 1,153.846 per day.
    expect($salary['per_day_rate'])->toBe(1153.85)
        ->and($salary['absence_deduction'])->toBe(2307.69)
        ->and($salary['unpaid_leave_deduction'])->toBe(1153.85)
        ->and($salary['total_deduction'])->toBe(3461.54)
        ->and($salary['net_salary'])->toBe(40538.46);
});

it('uses basic + allowances as the day rate when configured on gross', function () {
    $config = SalaryDetail::factory()->for($this->employee)->create(['effective_date' => '2025-06-01', 'deduction_basis' => 'gross']);

    $salary = calculateSalary($this->employee, '2026-01', ['present_days' => 25, 'absent_days' => 1], $config);

    // 44,000 / 26 = 1,692.31.
    expect($salary['absence_deduction'])->toBe(1692.31);
});

it('deducts exactly the base when absent every working day', function () {
    $salary = calculateSalary($this->employee, '2026-01', ['present_days' => 0, 'absent_days' => 26]);

    expect($salary['absence_deduction'])->toBe(30000.0)
        ->and($salary['net_salary'])->toBe(14000.0);
});

it('adds overtime, bonus and other additions, and applies fixed and monthly deductions', function () {
    $config = SalaryDetail::factory()->for($this->employee)->create([
        'effective_date' => '2025-06-01', 'overtime_rate' => 200, 'other_deduction' => 1500,
    ]);

    $salary = calculateSalary($this->employee, '2026-01', [
        'overtime_hours' => 10, 'bonus' => 5000, 'other_addition' => 300, 'other_deduction' => 200,
    ], $config);

    expect($salary['overtime_amount'])->toBe(2000.0)
        ->and($salary['gross_salary'])->toBe(51300.0)
        ->and($salary['other_deduction'])->toBe(1700.0)
        ->and($salary['net_salary'])->toBe(49600.0);
});

it('prorates basic and allowances for an employee who joined mid-month', function () {
    $employee = Employee::factory()->create(['joining_date' => '2026-01-16']);
    SalaryDetail::factory()->for($employee)->create(['effective_date' => '2026-01-16']);

    $salary = calculateSalary($employee, '2026-01', ['working_days' => 13, 'present_days' => 13]);

    // 16 of 31 days employed.
    expect($salary['employed_days'])->toBe(16)
        ->and($salary['basic_salary'])->toBe(15483.87)
        ->and($salary['gross_salary'])->toBe(22709.68);
});

it('prorates up to the last working date for an employee who left mid-month', function () {
    $employee = Employee::factory()->resigned('2026-01-10')->create(['joining_date' => '2025-01-01']);
    SalaryDetail::factory()->for($employee)->create(['effective_date' => '2025-01-01']);

    $salary = calculateSalary($employee, '2026-01', ['working_days' => 8, 'present_days' => 8]);

    expect($salary['employed_days'])->toBe(10)
        ->and($salary['basic_salary'])->toBe(9677.42);
});

it('uses the salary revision in force for each month', function () {
    SalaryDetail::factory()->for($this->employee)->create(['effective_date' => '2026-02-01', 'basic_salary' => 35000]);

    expect(calculateSalary($this->employee, '2026-01')['basic_salary'])->toBe(30000.0)
        ->and(calculateSalary($this->employee, '2026-02', ['working_days' => 24, 'present_days' => 24])['basic_salary'])->toBe(35000.0);
});

it('ignores inactive salary revisions', function () {
    SalaryDetail::factory()->for($this->employee)->create(['effective_date' => '2026-01-01', 'basic_salary' => 99999, 'status' => false]);

    expect(calculateSalary($this->employee, '2026-01')['basic_salary'])->toBe(30000.0);
});
