<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\PayslipMail;
use App\Models\EmployeeInfo;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\DataServices\{CompanyDataService};

class SendPayslipEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $employeeIds;
    protected $month;
    protected $year;
    protected $branchOfficeId;

    /**
     * The number of seconds the job can run before timing out.
     */
    public $timeout = 120; // 2 minutes

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(array $employeeIds, int $month, int $year, ?int $branchOfficeId = null)
    {
        $this->employeeIds = $employeeIds;
        $this->month = $month;
        $this->year = $year;
        $this->branchOfficeId = $branchOfficeId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('SendPayslipEmailJob started', [
            'employee_count' => count($this->employeeIds),
            'month' => $this->month,
            'year' => $this->year
        ]);

        $successCount = 0;
        $failCount = 0;

        foreach ($this->employeeIds as $empAutoId) {
            try {
                $this->sendPayslipToEmployee($empAutoId);
                $successCount++;
                Log::info("Payslip sent successfully for employee: {$empAutoId}");
            } catch (\Exception $e) {
                $failCount++;
                Log::error("Failed to send payslip for employee: {$empAutoId}", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        Log::info('SendPayslipEmailJob completed', [
            'success_count' => $successCount,
            'fail_count' => $failCount
        ]);
    }

    /**
     * Send payslip to a single employee.
     */
    protected function sendPayslipToEmployee(int $empAutoId): void
    {
        // Get employee salary data
        $salaryData = $this->getEmployeeSalaryData($empAutoId);

        if (!$salaryData) {
            throw new \Exception("No salary data found for employee ID: {$empAutoId}");
        }

        if (empty($salaryData->email)) {
            throw new \Exception("No email address found for employee ID: {$empAutoId}");
        }

        // Prepare payslip data
        $payslipData = $this->preparePayslipData($salaryData);

        // Generate PDF
        $pdf = Pdf::loadView('mails.emailed_payslip', $payslipData)->setPaper('a4', 'portrait');  // payroll::pages.Payroll.emailed_payslip
        // $pdf = Pdf::loadView('payroll::pages.payroll.mytest_email', $payslipData)
        //             ->setPaper('a4', 'portrait')
        //             ->setWarnings(false); // Helps prevent random layout logs from breaking the stream payslip payroll::pages.payroll.email_body_payslip

        $pdfContent = $pdf->output();
        Log::error($salaryData->employee_id." ".$salaryData->email);
        // Send email
        Mail::to($salaryData->email)
            ->send(new PayslipMail(
                $payslipData['employee'],
                ['month'=> $payslipData['salary_record']->month],
                $pdfContent
            ));
    }

    /**
     * Get employee salary data from database.
     */
    protected function getEmployeeSalaryData(int $employee_id): ?object
    {
        return DB::table('employee_infos')
            ->select([
                'employee_infos.emp_auto_id',
                'employee_infos.employee_id',
                'employee_infos.employee_name',
                'employee_infos.email',
                'employee_infos.akama_no',
                'employee_infos.joining_date',
                'employee_bank_details.acc_iban as iban',
                'employee_bank_details.acc_number as account_number',
                'bank_names.bank_code',
                'employee_categories.catg_name as designation',
                'project_infos.proj_name as project_name',
                'salary_histories.*',
                // 'salary_histories.slh_total_hours',
                // 'salary_histories.slh_total_overtime',
                // 'salary_histories.slh_overtime_amount',

                // 'salary_histories.partial_paid_amount',
                // 'salary_histories.slh_total_salary',
                // 'salary_histories.house_rent',
                // 'salary_histories.basic_amount',
                // 'salary_histories.mobile_allowance',
                // 'salary_histories.food_allowance',
                // 'salary_histories.slh_iqama_advance',
                // 'salary_histories.slh_other_advance',
                // 'salary_histories.slh_food_deduction',
                // 'salary_histories.slh_month',
                // 'salary_histories.slh_year',
                // 'salary_histories.local_travel_allowance as transport_allowance',
                // 'salary_histories.others as other_allowance',
                // 'salary_histories.slh_overtime_amount as overtime_amount',
                // 'salary_histories.slh_bonus_amount as bonus_amount',
            ])
            ->leftJoin('employee_bank_details', function ($join) {
                $join->on('employee_infos.emp_auto_id', '=', 'employee_bank_details.emp_auto_id')
                    ->where('employee_bank_details.is_active', 1);
            })
            ->leftJoin('bank_names', 'employee_bank_details.bank_id', '=', 'bank_names.bn_auto_id')
            ->leftJoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->leftJoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
            ->leftJoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
            ->where('employee_infos.employee_id', $employee_id)
            ->where('salary_histories.slh_month', $this->month)
            ->where('salary_histories.slh_year', $this->year)
            ->first();
    }

    /**
     * Prepare payslip data for PDF generation.
     */
    protected function preparePayslipData(object $salary_record): array
    {
        $company = (new CompanyDataService())->findCompanryProfile();

        $monthName =  date("F", mktime(0, 0, 0, $this->month, 10)); //$monthNames[$this->month] ?? 'Unknown';
        $daysInMonth = (int) date('t', strtotime("{$this->year}-{$this->month}-01"));

        $employee = [
            'employee_name' => $salary_record->employee_name ?? '',
            'name' => $salary_record->employee_name ?? '',
            'employee_id' => $salary_record->employee_id ?? '',
             'id' => $salary_record->employee_id ?? '',
            'designation' => $salary_record->designation ?? '',
            'department'=>'',
            'working_project' => $salary_record->project_name ?? '',
            'bank_code' => $salary_record->bank_code ?? '-',
            'account_no' => $salary_record->iban ?? $salary_record->account_number ?? '-'
        ];

        $company = [
            'name' => $company->comp_name_en, // config('app.company_name', 'Asloob Bedaa Contracting Company'),
            'location' => $company->comp_address,
           // 'com_logo_url' =>$company->com_logo,
        ];
        $salary_record->working_project =  $salary_record->project_name ?? '';
        $salary_record->department = '';
        $salary_record->account_no = $salary_record->iban ?? $salary_record->account_number ?? '-';
        $salary_record->month = $monthName . ' ' . $this->year;
        $salary_record->period = '01-' . substr($monthName, 0, 3) . '-' . $this->year . ' to ' . $daysInMonth . '-' . substr($monthName, 0, 3) . '-' . $this->year;
        $salary_record->pay_date = date('d-M-Y');
        $salary_record->currency ='SAR';
        $salary_record->paid_days =  $daysInMonth;
        $salary_record->calendar_days = $daysInMonth;
        $salary_record->lop_days = 0;
        $salary_record->amount_in_words = $this->convertNumberToWords(floatval($salary_record->slh_total_salary)) . ' Saudi Riyals Only';

        return compact('company','employee', 'salary_record');

    }

    /**
     * Convert number to words.
     */
    protected function convertNumberToWords(float $number): string
    {
        $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten',
            'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
        $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

        $number = floor($number);

        if ($number == 0) {
            return 'Zero';
        }

        if ($number < 20) {
            return $ones[$number];
        }

        if ($number < 100) {
            return $tens[floor($number / 10)] . ($number % 10 ? ' ' . $ones[$number % 10] : '');
        }

        if ($number < 1000) {
            return $ones[floor($number / 100)] . ' Hundred' . ($number % 100 ? ' ' . $this->convertNumberToWords($number % 100) : '');
        }

        if ($number < 100000) {
            return $this->convertNumberToWords(floor($number / 1000)) . ' Thousand' . ($number % 1000 ? ' ' . $this->convertNumberToWords($number % 1000) : '');
        }

        if ($number < 10000000) {
            return $this->convertNumberToWords(floor($number / 100000)) . ' Lakh' . ($number % 100000 ? ' ' . $this->convertNumberToWords($number % 100000) : '');
        }

        return $this->convertNumberToWords(floor($number / 10000000)) . ' Crore' . ($number % 10000000 ? ' ' . $this->convertNumberToWords($number % 10000000) : '');
    }
}

