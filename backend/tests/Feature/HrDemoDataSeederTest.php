<?php

use App\Models\Employee;
use App\Models\EmployeeWork;
use App\Models\SalaryDetail;
use App\Models\SalaryHistory;
use Database\Seeders\HrDemoDataSeeder;
use Illuminate\Support\Carbon;

it('seeds employees, work records and payroll at every stage, and can be run twice', function () {
    Carbon::setTestNow('2026-09-26');
    adminUser();

    $this->seed(HrDemoDataSeeder::class);
    $this->seed(HrDemoDataSeeder::class);

    $statuses = SalaryHistory::query()->selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');

    expect(Employee::count())->toBe(10)
        ->and(SalaryDetail::count())->toBe(12)
        ->and(EmployeeWork::whereDate('salary_month', '2026-09-01')->count())->toBe(6)
        // Eight employees are on each month's payroll; two have no September record yet.
        ->and($statuses['Paid'])->toBe(16)
        ->and($statuses['Approved'])->toBe(8)
        ->and($statuses['Generated'])->toBe(6)
        // The employee who left on 20 August is prorated for August.
        ->and(SalaryHistory::where('employee_code', 'EMP-1009')->whereDate('salary_month', '2026-08-01')->value('employed_days'))->toBe(20)
        // The raise from August does not touch the paid June salary.
        ->and((float) SalaryHistory::where('employee_code', 'EMP-1001')->whereDate('salary_month', '2026-06-01')->value('basic_salary'))->toBe(45000.0)
        ->and((float) SalaryHistory::where('employee_code', 'EMP-1001')->whereDate('salary_month', '2026-08-01')->value('basic_salary'))->toBe(50000.0);
});
