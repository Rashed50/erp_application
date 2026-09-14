<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\PayslipMail;
use App\Http\Controllers\DataServices\CompanyDataService;
use Carbon\Carbon;
use Throwable;

class SendSinglePayslipEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected ?int $empAutoId = null;
    protected int $employee_id;
    protected int $month;
    protected int $year;
    protected int $send_by_id;

    public int $timeout = 120; // 2 minutes per single email
    public int $tries = 2;

    public function __construct(int $employee_id, int $month, int $year,int $send_by_id)
    {
        $this->empAutoId = null;
        $this->employee_id = $employee_id;
        $this->month = $month;
        $this->year = $year;
        $this->send_by_id = $send_by_id;
    }

    public function initialInsertIntoDB(){

        // 1. Initial log entry or reset status to pending
        DB::table('payslip_email_logs')->updateOrInsert(
            [
                'employee_id' => $this->employee_id,
                'month'       => $this->month,
                'year'        => $this->year,
                'sent_by'      => $this->send_by_id ?? 0,
            ],
            [
                'status'         => 'pending',
                'failure_reason' => null,
                'updated_at'     => now(),
            ]
        );

    }

    public function handle(): void
    {

        $this->initialInsertIntoDB();
        try {
            // 2. Fetch employee data
            $salaryData = $this->getEmployeeSalaryData($this->employee_id);

            if (!$salaryData) {
                throw new \Exception("No salary record found for month {$this->month}/{$this->year}");
            }
            $this->empAutoId = $salaryData->emp_auto_id;

            if (empty($salaryData->email)) {
                throw new \Exception("Employee does not have a registered email address.");
            }

            // Update log with fetched email and employee string ID
            DB::table('payslip_email_logs')
                ->where('employee_id', $this->employee_id)
                ->where('month', $this->month)
                ->where('year', $this->year)
                ->update([
                    'emp_auto_id' => $salaryData->emp_auto_id,
                    'email'       => $salaryData->email
                ]);



            // 3. Generate PDF & Send Mail
            $payslipData = $this->preparePayslipData($salaryData);
            $pdf = Pdf::loadView('mails.emailed_payslip', $payslipData)->setPaper('a4', 'portrait');

            Mail::to($salaryData->email)->send(new PayslipMail(
                $payslipData['employee'],
                ['month' => $payslipData['salary_record']->month],
                $pdf->output()
            ));

            // 4. Mark status as SENT
            DB::table('payslip_email_logs')
                ->where('emp_auto_id', $this->empAutoId)
                ->where('month', $this->month)
                ->where('year', $this->year)
                ->update([
                    'status'     => 'sent',
                    'sent_at'    => now(),
                    'updated_at' => now(),
                ]);

            Log::error("Payslip email successfully sent to Employee Auto ID: {$this->employee_id}");

        } catch (Throwable $e) {
            // 5. Mark status as FAILED with exact reason
            DB::table('payslip_email_logs')
                ->where('employee_id', $this->employee_id)
                ->where('month', $this->month)
                ->where('year', $this->year)
                ->update([
                    'status'         => 'failed',
                    'failure_reason' => substr($e->getMessage(), 0, 500),
                    'updated_at'     => now(),
                ]);

            Log::error("Failed to send payslip email to Employee Auto ID: {$this->employee_id}. Reason: " . $e->getMessage());
            // Re-throw so Laravel Queue registers job retry attempt
            throw $e;
        }
    }

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
            ])
            ->leftJoin('employee_bank_details', function ($join) {
                $join->on('employee_infos.emp_auto_id', '=', 'employee_bank_details.emp_auto_id');
                   // ->where('employee_bank_details.is_active', 1);
            })
            ->leftJoin('bank_names', 'employee_bank_details.bank_id', '=', 'bank_names.bn_auto_id')
            ->leftJoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->leftJoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
            ->leftJoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
            ->where('employee_infos.employee_id', $employee_id)
            ->whereNotNull('employee_infos.email')
            ->where('salary_histories.slh_month', $this->month)
            ->where('salary_histories.slh_year', $this->year)
            ->first();
    }

    protected function preparePayslipData(object $salary_record): array
    {
        $companyProfile = (new CompanyDataService())->findCompanryProfile();
        $monthName = date("F", mktime(0, 0, 0, $this->month, 10));
        $daysInMonth = (int) date('t', strtotime("{$this->year}-{$this->month}-01"));

        $employee = [
            'employee_name'   => $salary_record->employee_name ?? '',
            'name'            => $salary_record->employee_name ?? '',
            'employee_id'     => $salary_record->employee_id ?? '',
            'id'              => $salary_record->employee_id ?? '',
            'designation'     => $salary_record->designation ?? '',
            'department'      => '',
            'working_project' => $salary_record->project_name ?? '',
            'bank_code'       => $salary_record->bank_code ?? '-',
            'account_no'      => $salary_record->iban ?? $salary_record->account_number ?? '-'
        ];

        $company = [
            'name'     => $companyProfile->comp_name_en ?? '',
            'location' => $companyProfile->comp_address ?? '',
        ];

        $salary_record->working_project = $salary_record->project_name ?? '';
        $salary_record->department = '';
        $salary_record->account_no = $salary_record->iban ?? $salary_record->account_number ?? '-';
        $salary_record->month = $monthName . ' ' . $this->year;
        $salary_record->period = '01-' . substr($monthName, 0, 3) . '-' . $this->year . ' to ' . $daysInMonth . '-' . substr($monthName, 0, 3) . '-' . $this->year;
        $salary_record->pay_date = date('d-M-Y');
        $salary_record->currency = 'SAR';
        $salary_record->paid_days = "-"; $daysInMonth;
        $salary_record->calendar_days = $daysInMonth;
        $salary_record->lop_days = 0;
        $salary_record->amount_in_words = $this->convertNumberToWords(floatval($salary_record->slh_total_salary)) . ' Saudi Riyals Only';

        return compact('company', 'employee', 'salary_record');
    }

    protected function convertNumberToWords(float $number): string
    {
        $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten',
            'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
        $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

        $number = floor($number);
        if ($number == 0) return 'Zero';
        if ($number < 20) return $ones[$number];
        if ($number < 100) return $tens[floor($number / 10)] . ($number % 10 ? ' ' . $ones[$number % 10] : '');
        if ($number < 1000) return $ones[floor($number / 100)] . ' Hundred' . ($number % 100 ? ' ' . $this->convertNumberToWords($number % 100) : '');
        if ($number < 100000) return $this->convertNumberToWords(floor($number / 1000)) . ' Thousand' . ($number % 1000 ? ' ' . $this->convertNumberToWords($number % 1000) : '');
        if ($number < 10000000) return $this->convertNumberToWords(floor($number / 100000)) . ' Lakh' . ($number % 100000 ? ' ' . $this->convertNumberToWords($number % 100000) : '');

        return $this->convertNumberToWords(floor($number / 10000000)) . ' Crore' . ($number % 10000000 ? ' ' . $this->convertNumberToWords($number % 10000000) : '');
    }
}
