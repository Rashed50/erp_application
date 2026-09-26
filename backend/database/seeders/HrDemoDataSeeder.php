<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\SalaryHistory;
use App\Models\User;
use App\Services\EmployeeService;
use App\Services\EmployeeWorkService;
use App\Services\PayrollService;
use App\Services\SalaryDetailService;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Demo HR data: ten employees, salary configurations (two with a raise),
 * four months of work records and payroll at every stage — the oldest two
 * months paid, last month approved, the current month generated with two
 * employees still pending.
 *
 * Everything goes through the application services, so the salaries are
 * produced by the real calculation and workflow.
 *
 * Run with: php artisan db:seed --class=HrDemoDataSeeder
 */
class HrDemoDataSeeder extends Seeder
{
    private const EMAIL_DOMAIN = '@demo-hr.example.com';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Employee::withTrashed()->where('email', 'like', '%'.self::EMAIL_DOMAIN)->exists()) {
            $this->command?->warn('HR demo data already exists, skipping.');

            return;
        }

        $admin = User::query()->where('email', env('SUPER_ADMIN_EMAIL', 'superadmin@example.com'))->first() ?? User::query()->first();
        if ($admin) {
            Auth::login($admin);
        }

        $currentMonth = CarbonImmutable::now()->startOfMonth();
        $months = collect([3, 2, 1, 0])->map(fn (int $monthsAgo) => $currentMonth->subMonths($monthsAgo));

        DB::transaction(function () use ($currentMonth, $months) {
            $employees = $this->seedEmployees($currentMonth);
            $this->seedWork($employees, $months, $currentMonth);
            $this->seedPayroll($months);
        });
    }

    /**
     * @return array<int, Employee>
     */
    private function seedEmployees(CarbonImmutable $currentMonth): array
    {
        $employeeService = app(EmployeeService::class);
        $salaryService = app(SalaryDetailService::class);

        $lastMonth = $currentMonth->subMonth();

        // [name, gender, department, designation, type, joining, basic, overtime rate, status, last working date]
        $people = [
            ['Abdul Karim', 'Male', 'Accounts', 'Accounts Manager', 'Permanent', '2022-02-01', 45000, 0, 'Active', null],
            ['Nusrat Jahan', 'Female', 'HR', 'HR Executive', 'Permanent', '2023-05-15', 28000, 0, 'Active', null],
            ['Tanvir Ahmed', 'Male', 'Sales', 'Sales Executive', 'Permanent', '2023-09-01', 25000, 150, 'Active', null],
            ['Farhana Akter', 'Female', 'Sales', 'Sales Officer', 'Permanent', '2024-01-10', 22000, 130, 'Active', null],
            ['Mahmudul Hasan', 'Male', 'Operations', 'Supervisor', 'Permanent', '2022-07-01', 30000, 180, 'Active', null],
            ['Sabbir Rahman', 'Male', 'Operations', 'Machine Operator', 'Contract', '2024-03-01', 18000, 120, 'Active', null],
            ['Rafiq Islam', 'Male', 'IT', 'Software Engineer', 'Permanent', '2023-11-01', 50000, 0, 'Active', null],
            // Joined in the middle of the current month: prorated.
            ['Sharmin Sultana', 'Female', 'Accounts', 'Accountant', 'Probation', $currentMonth->addDays(14)->toDateString(), 26000, 0, 'Active', null],
            // Left in the middle of last month: prorated last month, excluded now.
            ['Jahid Hasan', 'Male', 'Operations', 'Driver', 'Permanent', '2022-10-01', 16000, 100, 'Resigned', $lastMonth->addDays(19)->toDateString()],
            // Inactive since before the demo months: never on payroll.
            ['Mitu Das', 'Female', 'IT', 'Support Officer', 'Contract', '2024-02-01', 20000, 0, 'Inactive', $currentMonth->subMonths(4)->endOfMonth()->toDateString()],
        ];

        $employees = [];

        foreach ($people as $index => [$name, $gender, $department, $designation, $type, $joining, $basic, $overtimeRate, $status, $lastWorkingDate]) {
            $number = $index + 1;
            $isBank = $number % 3 !== 0;

            $employee = $employeeService->create([
                'employee_code' => 'EMP-'.(1000 + $number),
                'name' => $name,
                'father_name' => 'Md. '.explode(' ', $name)[1].' Sr.',
                'date_of_birth' => CarbonImmutable::create(1985 + $number, $number, 10)->toDateString(),
                'gender' => $gender,
                'phone' => '0171100'.str_pad((string) $number, 4, '0', STR_PAD_LEFT),
                'email' => strtolower(str_replace(' ', '.', $name)).self::EMAIL_DOMAIN,
                'address' => "House {$number}, Road ".($number + 2).', Dhaka',
                'joining_date' => $joining,
                'last_working_date' => $lastWorkingDate,
                'department' => $department,
                'designation' => $designation,
                'employment_type' => $type,
                'status' => $status,
                'detail' => [
                    'national_id' => '19'.str_pad((string) ($number * 7919), 11, '0', STR_PAD_LEFT),
                    'marital_status' => $number % 2 ? 'Married' : 'Single',
                    'blood_group' => ['A+', 'B+', 'O+', 'AB+', 'O-'][$index % 5],
                    'permanent_address' => "Village {$number}, Cumilla",
                    'payment_method' => $isBank ? 'Bank' : 'Cash',
                    'bank_name' => $isBank ? 'Demo Commercial Bank' : null,
                    'bank_branch' => $isBank ? 'Motijheel' : null,
                    'bank_account_name' => $isBank ? $name : null,
                    'bank_account_no' => $isBank ? '1001'.str_pad((string) $number, 8, '0', STR_PAD_LEFT) : null,
                    'emergency_contact_name' => 'Relative of '.$name,
                    'emergency_contact_relation' => $number % 2 ? 'Spouse' : 'Parent',
                    'emergency_contact_phone' => '0181100'.str_pad((string) $number, 4, '0', STR_PAD_LEFT),
                ],
            ]);

            $salaryService->create($employee, $this->salaryStructure($joining, $basic, $overtimeRate, $number));

            $employees[] = $employee;
        }

        // Raises effective from last month, so older months keep the old salary.
        foreach ([[0, 50000], [2, 28000]] as [$index, $basic]) {
            $salaryService->create($employees[$index], [
                ...$this->salaryStructure($lastMonth->toDateString(), $basic, $index === 2 ? 170 : 0, $index + 1),
                'remarks' => 'Annual increment',
            ]);
        }

        return $employees;
    }

    /**
     * @return array<string, mixed>
     */
    private function salaryStructure(string $effectiveDate, int $basic, int $overtimeRate, int $number): array
    {
        return [
            'effective_date' => $effectiveDate,
            'basic_salary' => $basic,
            'house_rent' => $basic * 0.5,
            'medical_allowance' => 2000,
            'transport_allowance' => 1500,
            'food_allowance' => 1000,
            'other_allowance' => 0,
            'overtime_rate' => $overtimeRate,
            // Provident fund for permanent staff with a higher basic.
            'other_deduction' => $basic >= 30000 ? round($basic * 0.05) : 0,
            'deduction_basis' => $number % 4 === 0 ? 'gross' : 'basic',
            'status' => true,
        ];
    }

    /**
     * Work records for every month each employee was employed. Two
     * employees have no record for the current month, so they show as
     * pending in the payroll preview.
     *
     * @param  array<int, Employee>  $employees
     * @param  Collection<int, CarbonImmutable>  $months
     */
    private function seedWork(array $employees, $months, CarbonImmutable $currentMonth): void
    {
        $workService = app(EmployeeWorkService::class);

        foreach ($months as $monthIndex => $month) {
            foreach ($employees as $index => $employee) {
                if (! $employee->isEmployedDuring($month)) {
                    continue;
                }

                if ($month->equalTo($currentMonth) && in_array($index, [4, 6], true)) {
                    continue;
                }

                $workingDays = $this->workingDays($employee, $month);
                $absent = ($index + $monthIndex) % 4 === 0 ? 1 : 0;
                $paidLeave = ($index * ($monthIndex + 1)) % 5 === 1 ? 1 : 0;
                $unpaidLeave = ($index === 5 && $monthIndex === 1) ? 2 : 0;
                $hasOvertime = in_array($employee->department, ['Sales', 'Operations'], true);

                $workService->create([
                    'employee_id' => $employee->id,
                    'salary_month' => $month->format('Y-m'),
                    'working_days' => $workingDays,
                    'present_days' => max(0, $workingDays - $absent - $paidLeave - $unpaidLeave),
                    'absent_days' => $absent,
                    'paid_leave_days' => $paidLeave,
                    'unpaid_leave_days' => $unpaidLeave,
                    'overtime_hours' => $hasOvertime ? 6 + (($index + $monthIndex) % 3) * 4 : 0,
                    // A performance bonus for sales in the second demo month.
                    'bonus' => $employee->department === 'Sales' && $monthIndex === 1 ? 3000 : 0,
                    'other_addition' => 0,
                    // A small salary advance recovered from one employee.
                    'other_deduction' => $index === 3 && $monthIndex === 2 ? 1000 : 0,
                    'remarks' => $absent ? 'Absent without notice' : null,
                ]);
            }
        }
    }

    /**
     * Calendar days in the employed part of the month, excluding Fridays.
     */
    private function workingDays(Employee $employee, CarbonImmutable $month): int
    {
        $from = $employee->joining_date->gt($month) ? CarbonImmutable::parse($employee->joining_date) : $month;
        $monthEnd = $month->endOfMonth()->startOfDay();
        $to = $employee->last_working_date && $employee->last_working_date->lt($monthEnd)
            ? CarbonImmutable::parse($employee->last_working_date)
            : $monthEnd;

        $days = 0;
        for ($day = $from; $day->lte($to); $day = $day->addDay()) {
            if (! $day->isFriday()) {
                $days++;
            }
        }

        return $days;
    }

    /**
     * @param  Collection<int, CarbonImmutable>  $months
     */
    private function seedPayroll($months): void
    {
        $payrollService = app(PayrollService::class);

        foreach ($months as $monthIndex => $month) {
            $payrollService->generate($month->format('Y-m'));

            $ids = SalaryHistory::query()->live()->whereDate('salary_month', $month->toDateString())->pluck('id')->all();

            // Oldest two months paid, last month approved, current month left as generated.
            if ($monthIndex <= 2) {
                $payrollService->approve($ids);
            }
            if ($monthIndex <= 1) {
                $payrollService->markPaid($ids);
            }
        }
    }
}
