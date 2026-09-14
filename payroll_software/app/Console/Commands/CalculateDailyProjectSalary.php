<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\CalculateDailyProjectSalaryJob;
use Carbon\Carbon;

class CalculateDailyProjectSalary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'salary:calculate-daily {--date= : The date to calculate for (format: Y-m-d, defaults to today)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate and store daily project-wise salaries based on employee attendance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dateInput = $this->option('date');

        if ($dateInput) {
            try {
                $date = Carbon::parse($dateInput);
            } catch (\Exception $e) {
                $this->error("Invalid date format. Please use Y-m-d format (e.g., 2026-01-05)");
                return 1;
            }
        } else {
            $date = Carbon::today();
        }

        $this->info("Starting daily project salary calculation for {$date->format('d/m/Y')}...");

        // Dispatch the job synchronously for immediate feedback
        CalculateDailyProjectSalaryJob::dispatchSync($date);

        $this->info("Daily project salary calculation completed successfully!");

        return 0;
    }
}

