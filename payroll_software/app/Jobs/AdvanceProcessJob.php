<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\DataServices\{SalaryProcessDataService,EmployeeAdvanceDataService,EmployeeDataService,FiscalYearDataService,ProjectDataService};
use Carbon\Carbon;


class AdvanceProcessJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $employees;
    protected $hourly_emp_direct_process;
    protected  $iqama_amount;
    protected  $other_amount;
    protected  $month;
    protected  $year;
    protected $counter = 0;
    protected $processing_mode=0;
    // The number of seconds the job can run before timing out
    public $timeout = 600; // 10 minutes
    // The number of times the job may be attempted
    public $tries = 3;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $iqama_amount,$other_amount,$direct_hourly_process,$month,$year,$processing_mode)
    {

         $this->iqama_amount = $iqama_amount;
         $this->other_amount = $other_amount;
         $this->hourly_emp_direct_process = $direct_hourly_process;
         $this->month = $month;
         $this->year = $year;
        $this->processing_mode = (int) $processing_mode;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{

                $hourly_emp_direct_process =  $this->hourly_emp_direct_process;
                $iqama_amount = $this->iqama_amount ;
                $other_amount = $this->other_amount;
                $employees_list = array();



                // if(  $this->processing_mode  == 1){
                //     // ASLOOB HOURLY EMPLOYEE
                //     $employees_list = (new EmployeeDataService())->getAllActiveAsloobSponsorHourlyEmployeesForIqamaAndAdvanceProcess();
                // }else if(  $this->processing_mode == 2)
                // {
                //     // ASLOOB BASIC EMPLOYEE
                //     $employees_list =   (new EmployeeDataService())->getAllActiveAsloobSponsorBasicSalaryEmployeesForIqamaAndAdvanceProcess();
                // }

                if(  $this->processing_mode  == 1 ||  $this->processing_mode == 2){
                    // All Employees who have work record of this month for advance process
                    //  $employees_list = (new EmployeeDataService())->getListOfEmployeesForIqamaAndOtherDeductionSetup($hourly_emp_direct_process,$this->month,$this->year);
                    $employees_list = (new EmployeeDataService())->getListOfEmployeesThoseHaveWorkRecordOfThisMonthForAdvanceProcess($this->month, $this->year);
                }



            Log::error('Advance Process mode and date '.$this->processing_mode .', time: '.Carbon::now());
            Log::error('Advance Processed total emp: '. count($employees_list));

              //  Log::info('Advance Process emp');
                foreach ($employees_list as $anEmployee) {

                     $fiscal = (new FiscalYearDataService())->getAnEmployeeRunningFiscalYearRecord($anEmployee->emp_auto_id);
                  // Advance Collection from employee
                    $deduction_record = (new SalaryProcessDataService())->getAdvanceAndIqamaRenewalDeductionTotalAmountForAdvanceProcess($anEmployee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);

                    $toal_iqama_expense_deduction_from_salary =  $deduction_record->iqama_deduction;
                    $cashReceiveTotalPaidAmount =  (new EmployeeAdvanceDataService())->getAnEmployeeAdvancePaidByCachTotalAmountByFiscalYear($anEmployee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);
                    // Advance Given to Employee
                    $iqama_renewal_cost_total_amount = (new EmployeeAdvanceDataService())->getAnEmployeeIqamaRenewalTotalExpenseAmountByFiscalYear($anEmployee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);
                    $iqama_renew_balance_amount =  $iqama_renewal_cost_total_amount   - ($toal_iqama_expense_deduction_from_salary + $cashReceiveTotalPaidAmount);

                    // Other Advance Calculation
                    // Advance Given to Employee
                    $otherAdvanceTotalAmount = (new EmployeeAdvanceDataService())->getAnEmployeeOthersAdvanceGivenTotalAmountByFiscalYear($anEmployee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);
                    // Advance Collection from Employee
                    $total_other_advace_deduction_from_salary = $deduction_record->other_deduction;
                    $other_advance_balance_amount =  $otherAdvanceTotalAmount -  $total_other_advace_deduction_from_salary ;

                    if(($other_advance_balance_amount) <= 0 ){
                        $anEmployee->other_adv_inst_amount = 0;
                    }
                    else if ($other_advance_balance_amount <= $other_amount) {
                         $anEmployee->other_adv_inst_amount = $other_advance_balance_amount;

                    }else if ($other_advance_balance_amount > $other_amount) {
                         $anEmployee->other_adv_inst_amount = $other_amount;
                    }

                    if(($iqama_renew_balance_amount) <= 0 ){
                        $anEmployee->iqama_adv_inst_amount = 0;
                    }
                    else if ($iqama_renew_balance_amount <= $iqama_amount) {
                        $anEmployee->iqama_adv_inst_amount = $iqama_renew_balance_amount;
                    }else if ($iqama_renew_balance_amount > $iqama_amount) {
                        $anEmployee->iqama_adv_inst_amount = $iqama_amount;
                    }
                    (new EmployeeDataService())->updateAnEmployeeIqamaAndOtherAdvaceInstallAmount($anEmployee->emp_auto_id,$anEmployee->iqama_adv_inst_amount, $anEmployee->other_adv_inst_amount);

                // to print other log have to configure and set value in .env
                Log::error('Advance Processed emp ID:'.$anEmployee->employee_id.' iqama :'.$anEmployee->iqama_adv_inst_amount.', other :'.$anEmployee->other_adv_inst_amount);


                }
        }catch (\Exception $e) {
            Log::error('Advance Process Job Failed: '.$e->getMessage());
        }
    }
}
