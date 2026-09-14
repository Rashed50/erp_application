<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

use App\Models\ProjectDailySalary;

class CalculateDailyProjectSalaryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $date;
    protected $day;
    protected $month;
    protected $year;

    /**
     * Create a new job instance.
     *
     * @param Carbon|null $date Optional date to calculate for (defaults to today)
     */
    public function __construct(Carbon $date = null)
    {
        $this->date = $date ?? Carbon::today();
        $this->day = $this->date->day;
        $this->month = $this->date->month;
        $this->year = $this->date->year;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info("Starting daily project salary calculation for {$this->day}/{$this->month}/{$this->year}");

            // Get all employees present today grouped by project using raw query for performance
            $projectWiseData = $this->getProjectWiseAttendanceData();

            foreach ($projectWiseData as $projectData) {
                $this->storeSalary($projectData);
            }

            Log::info("Completed daily project salary calculation for {$this->day}/{$this->month}/{$this->year}");

        } catch (\Exception $e) {
            Log::error("Error calculating daily project salary: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get attendance data grouped by project for the current date using raw query.
     *
     * @return array
     */
    private function getProjectWiseAttendanceData()
    {
        // I am Using raw query for maximum performance on large datasets
        $sql = "
            SELECT
                eio.proj_id,
                COUNT(DISTINCT eio.emp_id) as total_employees,
                SUM(
                    CASE
                        WHEN ei.emp_type_id = 1 THEN
                            (eio.daily_work_hours * IFNULL(sd.hourly_rent, 0)) +
                            (eio.over_time * IFNULL(sd.hourly_rent, 0) * 1.5)
                        ELSE
                            (IFNULL(sd.basic_amount, 0) / 30) +
                            (eio.over_time * IFNULL(sd.hourly_rent, 0) * 1.5) +
                            (IFNULL(sd.mobile_allowance, 0) / 30) +
                            (IFNULL(sd.food_allowance, 0) / 30)
                    END
                ) as total_salary
            FROM employee_in_outs eio
            INNER JOIN employee_infos ei ON eio.emp_id = ei.emp_auto_id
            LEFT JOIN salary_details sd ON ei.emp_auto_id = sd.emp_id
            WHERE eio.emp_io_date = ?
              AND eio.emp_io_month = ?
              AND eio.emp_io_year = ?
            GROUP BY eio.proj_id
        ";

        return DB::select($sql, [$this->day, $this->month, $this->year]);
    }

    /**
     * Store/update salary for a project.
     *
     * @param object $projectData
     */
    private function storeSalary($projectData)
    {
        $projectId = $projectData->proj_id;
        $totalSalary = round($projectData->total_salary ?? 0);
        $totalEmployees = $projectData->total_employees ?? 0;

        // Use updateOrCreate to handle both insert and update cases
        ProjectDailySalary::updateOrCreate(
            [
                'project_id' => $projectId,
                'day' => $this->day,
                'month' => $this->month,
                'year' => $this->year,
            ],
            [
                'total_salary' => $totalSalary,
                'total_employees' => $totalEmployees,
            ]
        );

        Log::info("Project {$projectId}: Salary={$totalSalary}, Employees={$totalEmployees}");
    }

    /**
     * Handle a job failure.
     *
     * @param \Throwable $exception
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("CalculateDailyProjectSalaryJob failed: " . $exception->getMessage());
    }
}
