<?php

namespace Modules\Dashboard\Services;

use App\Http\Controllers\DataServices\{EmployeeAttendanceDataService, ProjectDataService, EmployeeDataService};
use App\Models\ProjectDailySalary;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\DB;

class LiveDashboardService
{
    /**
     * Get today's present employee summary for all projects
     *
     * @param int $day
     * @param int $month
     * @param int $year
     * @return array
     */
    public function getTodayPresentEmployeeSummary($day, $month, $year)
    {
        $date = $year . '-' . $month . '-' . $day;
        $date = new DateTime($date);
        $previous_date = $date->modify('-1 day');
        $pre_day = (int) $previous_date->format('d');
        $pre_month = (int) $previous_date->format('m');
        $pre_year = (int) $previous_date->format('Y');

        $project_records = (new ProjectDataService())->getAllRunningProjectListForDropdown();
        $counter = 0;
        $final_list = array();
        $sponsorStatsAllProjects = DB::table('employee_in_outs as eio')
            ->join('employee_infos as ei', 'eio.emp_id', '=', 'ei.emp_auto_id')
            ->join('sponsors as s', 'ei.sponsor_id', '=', 's.spons_id')
            ->where('eio.emp_io_shift', 0)
            ->where('eio.emp_io_date', $day)
            ->where('eio.emp_io_month', $month)
            ->where('eio.emp_io_year', $year)
            ->whereIn('eio.attendance_type', [1,2,3,4,5,6])
            ->whereIn('s.owner_name', ['Asloob','Subcon'])
            ->groupBy('eio.proj_id', 's.owner_name')
            ->selectRaw("
                eio.proj_id,
                s.owner_name as owner_name,
                COUNT(DISTINCT eio.emp_id) as total_employees,
                COALESCE(SUM(eio.daily_work_hours),0) +
                COALESCE(SUM(eio.over_time),0) as total_hours
            ")
            ->get()
            ->groupBy('proj_id');

        $nightSponsorStatsAllProjects = DB::table('employee_in_outs as eio')
            ->join('employee_infos as ei', 'eio.emp_id', '=', 'ei.emp_auto_id')
            ->join('sponsors as s', 'ei.sponsor_id', '=', 's.spons_id')
            ->where('eio.emp_io_shift', 1) // night shift
            ->where('eio.emp_io_date', $pre_day)
            ->where('eio.emp_io_month', $pre_month)
            ->where('eio.emp_io_year', $pre_year)
            ->whereIn('eio.attendance_type', [1,2,3,4,5,6])
            ->whereIn('s.owner_name', ['Asloob','Subcon'])
            ->groupBy('eio.proj_id', 's.owner_name')
            ->selectRaw("
                eio.proj_id,
                s.owner_name as owner_name,
                COUNT(DISTINCT eio.emp_id) as total_employees,
                COALESCE(SUM(eio.daily_work_hours),0) +
                COALESCE(SUM(eio.over_time),0) as total_hours
            ")
            ->get()
            ->groupBy('proj_id');


        foreach ($project_records as $pid) {
            $data_found = false;
            $total_emps = (new EmployeeDataService())->countTotalActiveEmployeesInAProject($pid->proj_id, 0);
            $total_night_emps = (new EmployeeDataService())->countTotalActiveEmployeesInAProject($pid->proj_id, 1);

            if ($total_emps == 0 && $total_night_emps == 0) {
                continue;
            }

            // Day shift processing
            $day_shift_record = (new EmployeeAttendanceDataService())->getTodayPresentHourlyAndBasicEmployeeSummary($pid->proj_id, 0, $day, $month, $year);

            // Get total hours for day shift
            $day_total_hours = $this->getTotalHoursForProjectShift($pid->proj_id, 0, $day, $month, $year);

            $projectSponsors = $sponsorStatsAllProjects->get($pid->proj_id, collect());

            $asloob = $projectSponsors->firstWhere('owner_name', 'Asloob');
            $subcon = $projectSponsors->firstWhere('owner_name', 'Subcon');
            $pid->asloob = $asloob;
            $pid->subcon = $subcon;


            $asloobEmployees = (int) ($asloob->total_employees ?? 0);
            $subconEmployees = (int) ($subcon->total_employees ?? 0);

            $asloobHours = (float) ($asloob->total_hours ?? 0);
            $subconHours = (float) ($subcon->total_hours ?? 0);
            // --- End New ---

            if (count($day_shift_record) == 2) {
                $pid->day = [
                    'total_basic_emp' => (int) $day_shift_record[0]->total_emp,
                    'total_hourly_emp' => (int) $day_shift_record[1]->total_emp,
                    'workforce' => (int) $total_emps,
                    'total_hours' => (float) $day_total_hours,
                    'asloob' => [
                        'employees' => $asloobEmployees,
                        'total_hours' => $asloobHours,
                    ],

                    'subcon' => [
                        'employees' => $subconEmployees,
                        'total_hours' => $subconHours,
                    ],
                ];
                $data_found = true;
            } else if (count($day_shift_record) == 1) {
                $pid->day = [
                    'total_basic_emp' => (int) $day_shift_record[0]->total_emp,
                    'total_hourly_emp' => 0,
                    'workforce' => (int) $total_emps,
                    'total_hours' => (float) $day_total_hours,
                    'asloob' => [
                        'employees' => $asloobEmployees,
                        'total_hours' => $asloobHours,
                    ],

                    'subcon' => [
                        'employees' => $subconEmployees,
                        'total_hours' => $subconHours,
                    ],
                ];
                $data_found = true;
            } else {
                $pid->day = [
                    'total_basic_emp' => 0,
                    'total_hourly_emp' => 0,
                    'workforce' => (int) $total_emps,
                    'total_hours' => (float) $day_total_hours,
                    'asloob' => [
                        'employees' => $asloobEmployees,
                        'total_hours' => $asloobHours,
                    ],

                    'subcon' => [
                        'employees' => $subconEmployees,
                        'total_hours' => $subconHours,
                    ],
                ];
                $data_found = true;
            }

            // Night shift processing
            $night_shift_record = (new EmployeeAttendanceDataService())->getTodayPresentHourlyAndBasicEmployeeSummary($pid->proj_id, 1, $pre_day, $pre_month, $pre_year);

            // Get total hours for night shift
            $night_total_hours = $this->getTotalHoursForProjectShift($pid->proj_id, 1, $pre_day, $pre_month, $pre_year);
            $projectNightSponsors = $nightSponsorStatsAllProjects->get($pid->proj_id, collect());

            $nightAsloob = $projectNightSponsors->firstWhere('owner_name', 'Asloob');
            $nightSubcon = $projectNightSponsors->firstWhere('owner_name', 'Subcon');

            $nightAsloobEmployees = (int) ($nightAsloob->total_employees ?? 0);
            $nightSubconEmployees = (int) ($nightSubcon->total_employees ?? 0);

            $nightAsloobHours = (float) ($nightAsloob->total_hours ?? 0);
            $nightSubconHours = (float) ($nightSubcon->total_hours ?? 0);

            if ($night_shift_record == null) {
                $pid->night = [
                    'total_basic_emp' => 0,
                    'total_hourly_emp' => 0,
                    'workforce' => (int) $total_night_emps,
                    'total_hours' => (float) $night_total_hours,
                    'asloob' => [
                        'employees' => $nightAsloobEmployees,
                        'total_hours' => $nightAsloobHours,
                    ],

                    'subcon' => [
                        'employees' => $nightSubconEmployees,
                        'total_hours' => $nightSubconHours,
                    ],
                ];
                $data_found = true;
            } else if (count($night_shift_record) == 2) {
                $pid->night = [
                    'total_basic_emp' => (int) $night_shift_record[0]->total_emp,
                    'total_hourly_emp' => (int) $night_shift_record[1]->total_emp,
                    'workforce' => (int) $total_night_emps,
                    'total_hours' => (float) $night_total_hours,
                    'asloob' => [
                        'employees' => $nightAsloobEmployees,
                        'total_hours' => $nightAsloobHours,
                    ],

                    'subcon' => [
                        'employees' => $nightSubconEmployees,
                        'total_hours' => $nightSubconHours,
                    ],
                ];
                $data_found = true;
            } else if (count($night_shift_record) == 1) {
                $pid->night = [
                    'total_basic_emp' => (int) $night_shift_record[0]->total_emp,
                    'total_hourly_emp' => 0,
                    'workforce' => (int) $total_night_emps,
                    'total_hours' => (float) $night_total_hours,
                    'asloob' => [
                        'employees' => $nightAsloobEmployees,
                        'total_hours' => $nightAsloobHours,
                    ],

                    'subcon' => [
                        'employees' => $nightSubconEmployees,
                        'total_hours' => $nightSubconHours,
                    ],
                ];
                $data_found = true;
            }

            if ($data_found) {
                $final_list[$counter++] = $pid;
            }
        }

        return $final_list;
    }

    /**
     * Get total working hours for a project shift on a specific day
     *
     * @param int $projectId
     * @param int $workingShift (0 = day, 1 = night)
     * @param int $day
     * @param int $month
     * @param int $year
     * @return float
     */
    private function getTotalHoursForProjectShift($projectId, $workingShift, $day, $month, $year)
    {
        $result = DB::table('employee_in_outs')
            ->where('proj_id', $projectId)
            ->where('emp_io_shift', $workingShift)
            ->where('emp_io_date', $day)
            ->where('emp_io_month', $month)
            ->where('emp_io_year', $year)
            ->whereIn('attendance_type', [1, 2, 3, 4, 5, 6]) // Working attendance types
            ->select(DB::raw('COALESCE(SUM(daily_work_hours), 0) + COALESCE(SUM(over_time), 0) as total_hours'))
            ->first();

        return $result ? (float) $result->total_hours : 0;
    }

    /**
     * Get daily salary data for all projects for current month
     *
     * @return array
     */
    public function getProjectDailySalaryData()
    {
        $now = Carbon::now();
        $month = $now->month;
        $year = $now->year;
        $currentDay = $now->day;

        // Get all running projects
        $project_records = (new ProjectDataService())->getAllRunningProjectListForDropdown();

        // Fetch all salary records for current month in one query
        $salaryRecords = DB::table('project_daily_salaries')
            ->where('month', $month)
            ->where('year', $year)
            ->get()
            ->groupBy('project_id');

        foreach ($project_records as $project) {
            $dailySalary = [];
            $dailyEmployees = [];

            // Get salary records for this project
            $projectSalaries = $salaryRecords->get($project->proj_id, collect());

            // Create lookup by day
            $salaryByDay = $projectSalaries->keyBy('day');

            // Fill in salary data for each day up to current day
            for ($day = 1; $day <= $currentDay; $day++) {
                if ($salaryByDay->has($day)) {
                    $record = $salaryByDay->get($day);
                    $dailySalary[$day] = number_format($record->total_salary, 0);
                    $dailyEmployees[$day] = $record->total_employees ?? 0;
                } else {
                    $dailySalary[$day] = '-';
                    $dailyEmployees[$day] = 0;
                }
            }

            $project->dailySalary = $dailySalary;
            $project->dailyEmployees = $dailyEmployees;
        }

        return $project_records;
    }

    /**
     * Get live dashboard data
     *
     * @return array
     */
    public function getLiveDashboardData()
    {
        $now = Carbon::now();
        $day = $now->day;
        $month = $now->month;
        $year = $now->year;

        return [
            'attendance_datas' => $this->getTodayPresentEmployeeSummary($day, $month, $year),
            'salary_datas' => $this->getProjectDailySalaryData(),
            'month' => $now->format('F'),
            'year' => $now->year,
            'days_in_month' => $now->day,
        ];
    }

    /**
     * Get previous 3 months subcontractor data
     *
     * @param string $selectedDate
     * @return array
     */
    public function getPreviousThreeMonthSubcontractorData($selectedDate)
    {
        $selectedDate = Carbon::parse($selectedDate);

        // Get the previous 3 months
        $months = [];
        for ($i = 1; $i <= 3; $i++) {
            $month = $selectedDate->copy()->subMonths($i);
            $months[] = [
                'month' => $month->month,
                'year' => $month->year,
                'month_name' => $month->format('M'),
                'year_name' => $month->year,
            ];
        }

        // Get all subcontractors and their data for these months
        $subcontractors = DB::table('subcontractor_infos')
            ->select('subcon_auto_id', 'subcon_name')
            ->where('act_status', 1) // Active subcontractors
            ->limit(10) // Limit for dashboard display
            ->get();

        $reportData = [];
        foreach ($subcontractors as $subcontractor) {
            $subcontractorData = [
                'subcon_name' => $subcontractor->subcon_name,
                'months' => []
            ];

            foreach ($months as $monthData) {
                // Get invoice amount from subcontractor_services
                $invoiceAmount = DB::table('subcontractor_services')
                    ->where('subcon_auto_id', $subcontractor->subcon_auto_id)
                    ->where('month', $monthData['month'])
                    ->where('year', $monthData['year'])
                    ->sum('grand_total');

                // Get payment amount from subcontractors_payment
                $paymentAmount = DB::table('subcontractors_payment')
                    ->where('subcon_auto_id', $subcontractor->subcon_auto_id)
                    ->where('month', $monthData['month'])
                    ->where('year', $monthData['year'])
                    ->where('act_status', 1)
                    ->sum('grand_total');

                $subcontractorData['months'][] = [
                    'month_year' => $monthData['month_name'] . ', ' . $monthData['year_name'],
                    'invoice_amount' => $invoiceAmount,
                    'payment_amount' => $paymentAmount,
                ];
            }

            // Only include if there's some data
            $hasData = false;
            foreach ($subcontractorData['months'] as $month) {
                if ($month['invoice_amount'] > 0 || $month['payment_amount'] > 0) {
                    $hasData = true;
                    break;
                }
            }

            if ($hasData) {
                $reportData[] = $subcontractorData;
            }
        }

        return $reportData;
    }

    /**
     * Get previous 3 months employee salary data
     *
     * @param string $selectedDate
     * @return array
     */
    public function getPreviousThreeMonthEmployeeData($selectedDate)
    {
        $selectedDate = Carbon::parse($selectedDate);

        // Get the previous 3 months
        $months = [];
        for ($i = 1; $i <= 3; $i++) {
            $month = $selectedDate->copy()->subMonths($i);
            $months[] = [
                'month' => $month->month,
                'year' => $month->year,
                'month_name' => $month->format('M'),
                'year_name' => $month->year,
            ];
        }

        // Query salary data for these months
        $reportData = [];
        foreach ($months as $monthData) {
            $totalSalary = DB::table('salary_histories')
                ->where('slh_month', $monthData['month'])
                ->where('slh_year', $monthData['year'])
                ->sum('slh_total_salary');

            $paidSalary = DB::table('salary_histories')
                ->where('slh_month', $monthData['month'])
                ->where('slh_year', $monthData['year'])
                ->where('Status', 1)
                ->sum('slh_total_salary');

            $unpaidSalary = $totalSalary - $paidSalary;

            // Count total employees for this month
            $employeeCount = DB::table('salary_histories')
                ->where('slh_month', $monthData['month'])
                ->where('slh_year', $monthData['year'])
                ->count();

            // Count paid employees
            $paidEmployeeCount = DB::table('salary_histories')
                ->where('slh_month', $monthData['month'])
                ->where('slh_year', $monthData['year'])
                ->where('Status', 1)
                ->count();

            // Count unpaid employees
            $unpaidEmployeeCount = DB::table('salary_histories')
                ->where('slh_month', $monthData['month'])
                ->where('slh_year', $monthData['year'])
                ->where('Status', 0)
                ->count();

            $reportData[] = [
                'month_year' => $monthData['month_name'] . ', ' . $monthData['year_name'],
                'total_salary' => $totalSalary,
                'paid' => $paidSalary,
                'unpaid' => $unpaidSalary,
                'employee_count' => $employeeCount,
                'paid_employee_count' => $paidEmployeeCount,
                'unpaid_employee_count' => $unpaidEmployeeCount,
            ];
        }

        return $reportData;
    }

    /**
     * Get comprehensive dashboard data with previous 3 months reports
     *
     * @param string|null $selectedDate
     * @return array
     */
    public function getComprehensiveDashboardData($selectedDate = null, $view = 'attendance')
    {
        $now = Carbon::now();
        $selectedDate = $selectedDate ?: $now->toDateString();

        $response = [
            'last_updated' => $now->format('Y-m-d H:i:s'),
        ];

        switch ($view) {
            case 'attendance':
                $now = Carbon::now();
                $day = $now->day;
                $month = $now->month;
                $year = $now->year;

                $response['live_data'] = [
                    'attendance_datas' => $this->getTodayPresentEmployeeSummary($day, $month, $year),
                    'month'            => $now->format('F'),
                    'year'             => $now->year,
                    'days_in_month'    => $now->day,
                ];
                break;

            case 'salary':
                $now = Carbon::now();

                $response['live_data'] = [
                    'salary_datas'  => $this->getProjectDailySalaryData(),
                    'month'         => $now->format('F'),
                    'year'          => $now->year,
                    'days_in_month' => $now->day,
                ];
                break;
            case 'comprehensive':
                $response['subcontractor_data'] = $this->getPreviousThreeMonthSubcontractorData($selectedDate);
                $response['employee_data'] = $this->getPreviousThreeMonthEmployeeData($selectedDate);
                $response['salary_data'] = $this->getLastFourMonthSalaryAmount();
                $response['man_hours'] = $this->getLastFourMonthWorkinsManHours();
                break;
            default:
                // Default to attendance
                $response['live_data'] = $this->getLiveDashboardData();
                break;
        }

        return $response;
    }

      public function getLast3MonthAndYear(){
        $index = 0;

        $month = date('m', strtotime('-1 month'));
        $years[$index] = date('Y', strtotime('-1 month')); // Year (auto-adjusted)
        $months[$index] = $month;
        $month_names[$index] = date('M', strtotime('-1 month'));

        $index +=1; //1
        $month = date('m', strtotime('-2 month'));
        $years[$index] = date('Y', strtotime('-2 month')); // Year (auto-adjusted)
        $months[$index] = $month;
        $month_names[$index] = date('M', strtotime('-2 month'));

        $index +=1; //2
        $month = date('m', strtotime('-3 month'));
        $years[$index] = date('Y', strtotime('-3 month')); // Year (auto-adjusted)
        $months[$index] = $month;
        $month_names[$index] = date('M', strtotime('-3 month'));

        $index +=1;// 3
        $years[$index] = date('Y', strtotime('-4 month')); // Year (auto-adjusted)
        $months[$index] = date('m', strtotime('-4 month'));
        $month_names[$index] = date('M', strtotime('-4 month'));
        return [$months,$years,$month_names];

    }


    public function getLast4MonthAndYear(){
        $index = 0;
        // $month = date('m'); // current month
        // $years[0] = date('Y'); // current year
        // $months[0] = $month;
        // $month_names[0] = date('M');


        $month = date('m', strtotime('-1 month'));
        $years[$index] = date('Y', strtotime('-1 month')); // Year (auto-adjusted)
        $months[$index] = $month;
        $month_names[$index] = date('M', strtotime('-1 month'));
        $index +=1; //1

        $month = date('m', strtotime('-2 month'));
        $years[$index] = date('Y', strtotime('-2 month')); // Year (auto-adjusted)
        $months[$index] = $month;
        $month_names[$index] = date('M', strtotime('-2 month'));

        $index +=1; //2
        $month = date('m', strtotime('-3 month'));
        $years[$index] = date('Y', strtotime('-3 month')); // Year (auto-adjusted)
        $months[$index] = $month;
        $month_names[$index] = date('M', strtotime('-3 month'));

        $index +=1;// 3
        $years[$index] = date('Y', strtotime('-4 month')); // Year (auto-adjusted)
        $months[$index] = date('m', strtotime('-4 month'));
        $month_names[$index] = date('M', strtotime('-4 month'));


        return [$months,$years,$month_names];

    }

    public function getLast5MonthAndYear(){
       // $month = date('m'); // current month
        // $years[0] = date('Y'); // current year
        // $months[0] = $month;
        // $month_names[0] = date('M');
        $month = date('m', strtotime('-1 month'));
        $years[0] = date('Y', strtotime('-1 month')); // Year (auto-adjusted)
        $months[0] = $month;
        $month_names[0] = date('M', strtotime('-1 month'));


        $month = date('m', strtotime('-2 month'));
        $years[1] = date('Y', strtotime('-2 month')); // Year (auto-adjusted)
        $months[1] = $month;
        $month_names[1] = date('M', strtotime('-2 month'));

        $month = date('m', strtotime('-3 month'));
        $years[2] = date('Y', strtotime('-3 month')); // Year (auto-adjusted)
        $months[2] = $month;
        $month_names[2] = date('M', strtotime('-3 month'));

        $years[3] = date('Y', strtotime('-4 month')); // Year (auto-adjusted)
        $months[3] = date('m', strtotime('-4 month'));
        $month_names[3] = date('M', strtotime('-4 month'));

        $years[4] = date('Y', strtotime('-5 month')); // Year (auto-adjusted)
        $months[4] = date('m', strtotime('-5 month'));
        $month_names[4] = date('M', strtotime('-5 month'));

        return [$months,$years,$month_names];

    }

    public function getLastFourMonthSalaryAmount(){

        // Get the previous 3 months
         $month_year = $this->getLast4MonthAndYear( ); // current month
         $months =   $month_year[0];
         $years =   $month_year[1];
         $month_names =$month_year[2];
         $month_names[0] = $month_names[0].'-'.substr($years[0],-2);
         $month_names[1] = $month_names[1].'-'.substr($years[1],-2);
         $month_names[2] = $month_names[2].'-'.substr($years[2],-2);
         $month_names[3] = $month_names[3].'-'.substr($years[3],-2);


        $conditions = [
            ['month' => $months[0], 'year' => $years[0]],
            ['month' => $months[1], 'year' => $years[1]],
            ['month' => $months[2], 'year' => $years[2]],
            ['month' => $months[3], 'year' => $years[3]],
        ];

        $salaryArray = DB::table('salary_histories')
                    ->select('slh_month', 'slh_year', DB::raw('SUM(slh_total_salary) as slh_total_salary'))
                    ->where(function ($q) use ($conditions) {
                        foreach ($conditions as $c) {
                            $q->orWhere(function ($sub) use ($c) {
                                $sub->where('slh_month', $c['month'])
                                    ->where('slh_year', $c['year']);
                            });
                        }
                    })
                    ->whereIn('Status', [0, 1])
                    ->groupBy('slh_year', 'slh_month')
                    ->orderBy('slh_year', 'desc')
                    ->orderBy('slh_month', 'desc')
                    ->get();

        $total_salary = collect($salaryArray)->pluck('slh_total_salary')
                        ->map(fn($value) => round($value/100000,3))
                        ->toArray();

        $salaryArray = DB::table('salary_histories')
                    ->select('slh_month', 'slh_year', DB::raw('SUM(slh_total_salary) as slh_total_salary'))
                    ->where(function ($q) use ($conditions) {
                        foreach ($conditions as $c) {
                            $q->orWhere(function ($sub) use ($c) {
                                $sub->where('slh_month', $c['month'])
                                    ->where('slh_year', $c['year']);
                            });
                        }
                    })
                    ->where('Status', 1)
                    ->groupBy('slh_year', 'slh_month')
                    ->orderBy('slh_year', 'desc')
                    ->orderBy('slh_month', 'desc')
                    ->get();

        $total_paid = collect($salaryArray)->pluck('slh_total_salary')
                        ->map(fn($value) =>  round($value/100000,3))
                        ->toArray();

        return [
            'total_salary' => $total_salary,
            'total_paid' => $total_paid,
            'month_names' => $month_names
         //   'last_updated' => $now->format('Y-m-d H:i:s'),
        ];
    }

     public function getLastFourMonthWorkinsManHours(){

        // Get the previous 3 months
         $month_year = $this->getLast5MonthAndYear( ); // current month
         $months =   $month_year[0];
         $years =   $month_year[1];
         $month_names =$month_year[2];
         $month_names[0] = $month_names[0].'-'.substr($years[0],-2);
         $month_names[1] = $month_names[1].'-'.substr($years[1],-2);
         $month_names[2] = $month_names[2].'-'.substr($years[2],-2);
         $month_names[3] = $month_names[3].'-'.substr($years[3],-2);
         $month_names[4] = $month_names[4].'-'.substr($years[4],-2);



            $conditions = [
                ['month' => $months[0], 'year' => $years[0]],
                ['month' => $months[1], 'year' => $years[1]],
                ['month' => $months[2], 'year' => $years[2]],
                ['month' => $months[3], 'year' => $years[3]],
                ['month' => $months[4], 'year' => $years[4]],
            ];

            $query = DB::table('emp_multi_proj_work_hist')
                ->select('month', 'year', DB::raw('COUNT(emp_id) as total_employees'), DB::raw('SUM(total_hour+total_overtime) as total_hours'));

            $query->where(function($q) use ($conditions) {
                foreach ($conditions as $condition) {
                    $q->orWhere(function($subQ) use ($condition) {
                        $subQ->where('month', $condition['month'])
                            ->where('year', $condition['year']);
                    });
                }
            });


             $results = $query->groupBy('year', 'month')
                              ->orderBy('year', 'desc')
                              ->orderBy('month', 'desc')
                              ->get();
             $manHoursCollection = collect($results);
            return [
                'total_hours' =>$manHoursCollection->pluck('total_hours')->map(fn($value) =>round($value,2))->toArray(),
                'total_employees' =>$manHoursCollection->pluck('total_employees')->toArray(),
                'month_names'=> $month_names,
            ];


    }



}
