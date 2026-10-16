<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\User;
use App\Services\EmployeeService;
use App\Services\EmployeeWorkService;
use App\Services\SalaryDetailService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Fifteen employees with realistic profiles, salary structures and a
 * September 2026 work record each, ready for salary generation.
 * No salary is generated here.
 *
 * Working days are the month's calendar days (30 for September), so a
 * 12,000 basic pays 400 per day.
 *
 * Run with: php artisan db:seed --class=EmployeeSeeder
 */
class EmployeeSeeder extends Seeder
{
    private const WORK_MONTH = '2026-09';

    private const WORKING_DAYS = 30;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Employee::withTrashed()->where('employee_code', 'EMP-2001')->exists()) {
            $this->command?->warn('Employees EMP-2001..EMP-2015 already exist, skipping.');

            return;
        }

        $admin = User::query()->where('email', env('SUPER_ADMIN_EMAIL', 'superadmin@example.com'))->first() ?? User::query()->first();
        if ($admin) {
            Auth::login($admin);
        }

        DB::transaction(function () {
            foreach ($this->people() as $index => $person) {
                $this->seedEmployee($index + 1, $person);
            }
        });

        $this->command?->info('15 employees seeded with salary structures and '.self::WORK_MONTH.' work records.');
    }

    /**
     * @param  array<string, mixed>  $p
     */
    private function seedEmployee(int $number, array $p): void
    {
        $code = 'EMP-'.(2000 + $number);
        $isBank = $p['payment'] === 'Bank';

        $employee = app(EmployeeService::class)->create([
            'employee_code' => $code,
            'name' => $p['name'],
            'father_name' => $p['father'],
            'date_of_birth' => $p['dob'],
            'gender' => $p['gender'],
            'phone' => $p['phone'],
            'email' => strtolower(str_replace(['.', ' '], ['', '.'], $p['name'])).'@example.com',
            'address' => $p['address'],
            'joining_date' => $p['joining'],
            'last_working_date' => null,
            'department_id' => Department::firstOrCreate(['name' => $p['department']])->id,
            'designation_id' => Designation::firstOrCreate(['name' => $p['designation']])->id,
            'employment_type' => $p['type'],
            'status' => 'Active',
            'detail' => [
                'national_id' => $p['nid'],
                'marital_status' => $p['marital'],
                'blood_group' => $p['blood'],
                'permanent_address' => $p['permanent'],
                'payment_method' => $p['payment'],
                'emergency_contact_name' => $p['emergency'][0],
                'emergency_contact_relation' => $p['emergency'][1],
                'emergency_contact_phone' => $p['emergency'][2],
            ],
            'bank' => $isBank ? [
                'bank_name' => $p['bank'][0],
                'branch_name' => $p['bank'][1],
                'account_name' => $p['name'],
                'account_no' => $p['bank'][2],
            ] : [],
        ]);

        [$basic, $house, $medical, $transport, $food, $overtimeRate, $fixedDeduction] = $p['salary'];

        app(SalaryDetailService::class)->create($employee, [
            'effective_date' => $p['joining'],
            'basic_salary' => $basic,
            'house_rent' => $house,
            'medical_allowance' => $medical,
            'transport_allowance' => $transport,
            'food_allowance' => $food,
            'other_allowance' => 0,
            'overtime_rate' => $overtimeRate,
            'other_deduction' => $fixedDeduction,
            'deduction_basis' => 'basic',
            'status' => true,
        ]);

        [$workingDays, $present, $absent, $paidLeave, $unpaidLeave, $overtimeHours, $bonus, $remarks] = $p['work'];

        app(EmployeeWorkService::class)->create([
            'employee_id' => $employee->id,
            'salary_month' => self::WORK_MONTH,
            'working_days' => $workingDays,
            'present_days' => $present,
            'absent_days' => $absent,
            'paid_leave_days' => $paidLeave,
            'unpaid_leave_days' => $unpaidLeave,
            'overtime_hours' => $overtimeHours,
            'bonus' => $bonus,
            'other_addition' => 0,
            'other_deduction' => 0,
            'remarks' => $remarks,
        ]);
    }

    /**
     * salary: [basic, house rent, medical, transport, food, overtime rate, fixed deduction (PF)]
     * work:   [working, present, absent, paid leave, unpaid leave, overtime hours, bonus, remarks]
     *
     * @return array<int, array<string, mixed>>
     */
    private function people(): array
    {
        $full = self::WORKING_DAYS;

        return [
            [
                'name' => 'Md. Rashedul Islam', 'father' => 'Md. Abdur Rahim', 'dob' => '1984-03-12', 'gender' => 'Male',
                'phone' => '01711-234567', 'nid' => '1984261234567', 'marital' => 'Married', 'blood' => 'B+',
                'address' => 'House 12, Road 5, Dhanmondi, Dhaka', 'permanent' => 'Vill: Chandina, Cumilla',
                'department' => 'Administration', 'designation' => 'General Manager', 'type' => 'Permanent', 'joining' => '2019-01-01',
                'payment' => 'Bank', 'bank' => ['Dutch-Bangla Bank PLC', 'Dhanmondi', '1101510045671'],
                'emergency' => ['Shahnaz Parvin', 'Spouse', '01819-445566'],
                'salary' => [60000, 30000, 3000, 3000, 2000, 0, 3000],
                'work' => [$full, $full, 0, 0, 0, 0, 0, null],
            ],
            [
                'name' => 'Farzana Akter', 'father' => 'Md. Jalal Uddin', 'dob' => '1990-07-22', 'gender' => 'Female',
                'phone' => '01712-345678', 'nid' => '1990269876543', 'marital' => 'Married', 'blood' => 'O+',
                'address' => 'Flat 4B, Mirpur 10, Dhaka', 'permanent' => 'Vill: Sreepur, Gazipur',
                'department' => 'Accounts', 'designation' => 'Accounts Manager', 'type' => 'Permanent', 'joining' => '2020-03-15',
                'payment' => 'Bank', 'bank' => ['BRAC Bank PLC', 'Mirpur', '1501203345678'],
                'emergency' => ['Md. Kamrul Hasan', 'Spouse', '01913-778899'],
                'salary' => [45000, 22500, 2500, 2000, 1500, 0, 2250],
                'work' => [$full, 28, 0, 2, 0, 0, 0, 'Two days paid leave'],
            ],
            [
                'name' => 'Md. Tanvir Hossain', 'father' => 'Md. Nurul Hossain', 'dob' => '1993-11-05', 'gender' => 'Male',
                'phone' => '01713-456789', 'nid' => '1993261122334', 'marital' => 'Single', 'blood' => 'A+',
                'address' => 'House 7, Sector 11, Uttara, Dhaka', 'permanent' => 'Vill: Shibchar, Madaripur',
                'department' => 'Accounts', 'designation' => 'Accountant', 'type' => 'Permanent', 'joining' => '2022-06-01',
                'payment' => 'Bank', 'bank' => ['Islami Bank Bangladesh PLC', 'Uttara', '2050177700123'],
                'emergency' => ['Md. Nurul Hossain', 'Father', '01715-112233'],
                'salary' => [28000, 14000, 1500, 1500, 1000, 0, 0],
                'work' => [$full, 27, 3, 0, 0, 0, 0, 'Absent 3 days'],
            ],
            [
                'name' => 'Nusrat Jahan', 'father' => 'Md. Abul Kalam', 'dob' => '1995-02-18', 'gender' => 'Female',
                'phone' => '01714-567890', 'nid' => '1995264455667', 'marital' => 'Single', 'blood' => 'AB+',
                'address' => 'House 22, Mohammadpur, Dhaka', 'permanent' => 'Vill: Kotalipara, Gopalganj',
                'department' => 'HR', 'designation' => 'HR Executive', 'type' => 'Permanent', 'joining' => '2023-01-10',
                'payment' => 'Bank', 'bank' => ['City Bank PLC', 'Mohammadpur', '1252003456701'],
                'emergency' => ['Rokeya Begum', 'Mother', '01716-998877'],
                'salary' => [25000, 12500, 1500, 1500, 1000, 0, 0],
                'work' => [$full, 29, 0, 1, 0, 0, 0, null],
            ],
            [
                'name' => 'Md. Sohel Rana', 'father' => 'Md. Lutfar Rahman', 'dob' => '1991-09-30', 'gender' => 'Male',
                'phone' => '01715-678901', 'nid' => '1991267788990', 'marital' => 'Married', 'blood' => 'O+',
                'address' => 'House 3, Badda, Dhaka', 'permanent' => 'Vill: Sherpur Sadar, Sherpur',
                'department' => 'Sales', 'designation' => 'Sales Manager', 'type' => 'Permanent', 'joining' => '2021-04-01',
                'payment' => 'Bank', 'bank' => ['Dutch-Bangla Bank PLC', 'Badda', '1101510098765'],
                'emergency' => ['Taslima Akter', 'Spouse', '01817-334455'],
                'salary' => [40000, 20000, 2000, 3000, 1500, 0, 2000],
                'work' => [$full, $full, 0, 0, 0, 0, 5000, 'Sales target bonus'],
            ],
            [
                'name' => 'Sharmin Sultana', 'father' => 'Md. Mofizul Islam', 'dob' => '1997-05-14', 'gender' => 'Female',
                'phone' => '01716-789012', 'nid' => '1997262233445', 'marital' => 'Single', 'blood' => 'B-',
                'address' => 'House 9, Rampura, Dhaka', 'permanent' => 'Vill: Bhairab, Kishoreganj',
                'department' => 'Sales', 'designation' => 'Sales Executive', 'type' => 'Permanent', 'joining' => '2023-08-01',
                'payment' => 'Bank', 'bank' => ['BRAC Bank PLC', 'Rampura', '1501203398712'],
                'emergency' => ['Md. Mofizul Islam', 'Father', '01718-667788'],
                'salary' => [18000, 9000, 1000, 1500, 1000, 120, 0],
                'work' => [$full, 26, 2, 0, 2, 10, 2000, 'Two days unpaid leave'],
            ],
            [
                'name' => 'Md. Arif Hasan', 'father' => 'Md. Shamsul Haque', 'dob' => '1998-12-01', 'gender' => 'Male',
                'phone' => '01717-890123', 'nid' => '1998265566778', 'marital' => 'Single', 'blood' => 'A-',
                'address' => 'House 15, Khilgaon, Dhaka', 'permanent' => 'Vill: Daudkandi, Cumilla',
                'department' => 'Sales', 'designation' => 'Sales Representative', 'type' => 'Contract', 'joining' => '2024-02-01',
                'payment' => 'Cash',
                'emergency' => ['Md. Shamsul Haque', 'Father', '01719-223344'],
                // The 12,000 example: 12,000 / 30 = 400 per day.
                'salary' => [12000, 5000, 1000, 1000, 1000, 100, 0],
                'work' => [$full, 20, 10, 0, 0, 0, 0, 'Absent 10 days'],
            ],
            [
                'name' => 'Md. Kamal Uddin', 'father' => 'Md. Jamal Uddin', 'dob' => '1986-08-20', 'gender' => 'Male',
                'phone' => '01718-901234', 'nid' => '1986268899001', 'marital' => 'Married', 'blood' => 'O+',
                'address' => 'House 40, Tongi, Gazipur', 'permanent' => 'Vill: Nabinagar, Brahmanbaria',
                'department' => 'Production', 'designation' => 'Production Supervisor', 'type' => 'Permanent', 'joining' => '2018-10-01',
                'payment' => 'Bank', 'bank' => ['Sonali Bank PLC', 'Tongi', '0102033004567'],
                'emergency' => ['Hasina Begum', 'Spouse', '01911-556677'],
                'salary' => [30000, 15000, 2000, 1500, 1500, 180, 1500],
                'work' => [$full, 29, 1, 0, 0, 24, 0, null],
            ],
            [
                'name' => 'Md. Rubel Mia', 'father' => 'Md. Abdul Malek', 'dob' => '1994-04-11', 'gender' => 'Male',
                'phone' => '01719-012345', 'nid' => '1994260011223', 'marital' => 'Married', 'blood' => 'B+',
                'address' => 'Ershad Nagar, Tongi, Gazipur', 'permanent' => 'Vill: Gaibandha Sadar, Gaibandha',
                'department' => 'Production', 'designation' => 'Machine Operator', 'type' => 'Permanent', 'joining' => '2021-11-15',
                'payment' => 'Cash',
                'emergency' => ['Mst. Rina Begum', 'Spouse', '01912-889900'],
                'salary' => [15000, 6000, 1000, 1000, 1500, 110, 0],
                'work' => [$full, 28, 1, 1, 0, 32, 0, null],
            ],
            [
                'name' => 'Mst. Shapla Khatun', 'father' => 'Md. Habibur Rahman', 'dob' => '1996-06-25', 'gender' => 'Female',
                'phone' => '01720-123456', 'nid' => '1996263344556', 'marital' => 'Married', 'blood' => 'A+',
                'address' => 'Cherag Ali, Tongi, Gazipur', 'permanent' => 'Vill: Ullapara, Sirajganj',
                'department' => 'Production', 'designation' => 'Machine Operator', 'type' => 'Permanent', 'joining' => '2022-03-01',
                'payment' => 'Cash',
                'emergency' => ['Md. Selim Reza', 'Spouse', '01913-990011'],
                'salary' => [14000, 5600, 1000, 1000, 1500, 100, 0],
                'work' => [$full, 25, 3, 0, 2, 16, 0, 'Absent 3 days, 2 days unpaid leave'],
            ],
            [
                'name' => 'Md. Jahangir Alam', 'father' => 'Md. Siraj Mia', 'dob' => '1989-01-08', 'gender' => 'Male',
                'phone' => '01721-234567', 'nid' => '1989266677889', 'marital' => 'Married', 'blood' => 'O-',
                'address' => 'House 5, Shyamoli, Dhaka', 'permanent' => 'Vill: Bhola Sadar, Bhola',
                'department' => 'Store', 'designation' => 'Store Keeper', 'type' => 'Permanent', 'joining' => '2020-09-01',
                'payment' => 'Bank', 'bank' => ['Islami Bank Bangladesh PLC', 'Shyamoli', '2050177700456'],
                'emergency' => ['Mst. Salma Akter', 'Spouse', '01714-121212'],
                'salary' => [20000, 10000, 1500, 1000, 1000, 0, 0],
                'work' => [$full, $full, 0, 0, 0, 0, 0, null],
            ],
            [
                'name' => 'Md. Habibullah', 'father' => 'Md. Ataur Rahman', 'dob' => '1987-10-16', 'gender' => 'Male',
                'phone' => '01722-345678', 'nid' => '1987269900112', 'marital' => 'Married', 'blood' => 'B+',
                'address' => 'Kamrangirchar, Dhaka', 'permanent' => 'Vill: Muktagacha, Mymensingh',
                'department' => 'Transport', 'designation' => 'Driver', 'type' => 'Permanent', 'joining' => '2019-07-01',
                'payment' => 'Cash',
                'emergency' => ['Mst. Parvin Akter', 'Spouse', '01815-343434'],
                'salary' => [16000, 6400, 1000, 0, 1500, 120, 0],
                'work' => [$full, 29, 0, 0, 1, 40, 0, 'One day unpaid leave'],
            ],
            [
                'name' => 'Md. Ripon Sheikh', 'father' => 'Md. Hanif Sheikh', 'dob' => '2000-02-29', 'gender' => 'Male',
                'phone' => '01723-456789', 'nid' => '2000261234098', 'marital' => 'Single', 'blood' => 'AB-',
                'address' => 'Lalbagh, Dhaka', 'permanent' => 'Vill: Tungipara, Gopalganj',
                'department' => 'Administration', 'designation' => 'Office Assistant', 'type' => 'Contract', 'joining' => '2024-05-01',
                'payment' => 'Cash',
                'emergency' => ['Md. Hanif Sheikh', 'Father', '01716-565656'],
                'salary' => [10000, 4000, 500, 500, 1000, 0, 0],
                'work' => [$full, 27, 2, 1, 0, 0, 0, null],
            ],
            [
                'name' => 'Md. Imran Khan', 'father' => 'Md. Aminul Khan', 'dob' => '1992-12-24', 'gender' => 'Male',
                'phone' => '01724-567890', 'nid' => '1992265678901', 'marital' => 'Married', 'blood' => 'A+',
                'address' => 'House 18, Bashundhara R/A, Dhaka', 'permanent' => 'Vill: Sonargaon, Narayanganj',
                'department' => 'IT', 'designation' => 'Software Engineer', 'type' => 'Permanent', 'joining' => '2022-01-03',
                'payment' => 'Bank', 'bank' => ['Eastern Bank PLC', 'Bashundhara', '1041060789012'],
                'emergency' => ['Sadia Afrin', 'Spouse', '01717-787878'],
                'salary' => [50000, 25000, 2500, 2500, 2000, 0, 2500],
                'work' => [$full, 28, 0, 2, 0, 0, 0, null],
            ],
            [
                'name' => 'Tasnim Rahman', 'father' => 'Md. Mizanur Rahman', 'dob' => '1999-09-09', 'gender' => 'Female',
                'phone' => '01725-678901', 'nid' => '1999267890123', 'marital' => 'Single', 'blood' => 'O+',
                'address' => 'House 2, Banasree, Dhaka', 'permanent' => 'Vill: Savar, Dhaka',
                'department' => 'IT', 'designation' => 'Support Officer', 'type' => 'Probation', 'joining' => '2026-09-15',
                'payment' => 'Bank', 'bank' => ['BRAC Bank PLC', 'Banasree', '1501203321098'],
                'emergency' => ['Md. Mizanur Rahman', 'Father', '01718-909090'],
                // Joined 15 September: employed 16 of 30 days, so salary is prorated.
                'salary' => [22000, 11000, 1000, 1000, 1000, 0, 0],
                'work' => [16, 16, 0, 0, 0, 0, 0, 'Joined 15 September'],
            ],
        ];
    }
}
