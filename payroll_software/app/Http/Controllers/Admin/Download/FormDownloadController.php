<?php

namespace App\Http\Controllers\Admin\Download;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\DataServices\EmployeeDataService;
use App\Http\Controllers\DataServices\ProjectDataService;
use App\Http\Controllers\DataServices\CompanyDataService;
use App\Http\Controllers\DataServices\{EmployeeAdvanceDataService,SalaryProcessDataService};
use App\Http\Controllers\DataServices\EmployeePromotionDataService;
use App\Http\Controllers\Admin\Helper\HelperController;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use stdClass; // Import stdClass from the global namespace

class FormDownloadController extends Controller
{

    public function loadFormGenerateAndDownloadUI(){

        $projects = (new ProjectDataService())->getAllActiveProjectListForDropdown();
        return view('admin.download_form.generate_form',compact('projects'));

    }


    public function createEmpIncrementForm(Request $request){

        $company = (new CompanyDataService())->findCompanryProfile();
        $prepared_by = Auth::user()->name;
        // dd($company);

       // dd($request->all(),$company);
        if($request->form_type == 1){

            $employee = (new EmployeeDataService())->searchAnEmployeeWithImportantInformationAsShowByMultitypeParameter($request->employee_id,'employee_id',Auth::user()->branch_office_id);
            if(!$employee){
                return "Employee Not Found ";
            }
            $last_increment = (new EmployeePromotionDataService())->getAnEmployeePromotionLastRecords($request->employee_id);
            $employee->last_increment_date = "";
           $employee->last_increment_amount  = 0;

           if($last_increment){
               $employee->last_increment_date = $last_increment->prom_date;
               $employee->last_increment_amount  = $last_increment->increment_amount;
           }

            $declaration_text = "";
            $form_title = "EMPLOYEE  PERFORMANCE  EVALUATION  FORM";
            $employee->increment_duration = $request->duration;
            $employee->new_salary_type  = $request->new_salary_type;
            $effective_date = $request->effective_date;
            $remarks = $request->remarks;
            $amount = $request->amount;

            $in_word = (new HelperController())->numberToWord($amount);


            return view('admin.download_form.increment_form',compact('employee','company','amount','in_word','effective_date','prepared_by','remarks','declaration_text','form_title'));


        }

    }

    // Create Employee Advance Paper OR Employee Cash Received FORM
    public function createEmployeeAdvancePaper(Request $request)
    {

        if($request->request_type == 2){
            // cash receive form
            $employee = (new EmployeeDataService())->searchAnEmployeeWithImportantInformationAsShowByMultitypeParameter($request->employee_id,'employee_id',Auth::user()->branch_office_id);
             if(!$employee){
                return "Employee Not Found ";
            }

            $declaration_text = "";
            $form_title = "";

            $payment_method = $request->payment_method;
            $receiver_type = $request->receiver_type;
            $remarks = $request->remarks;
            $received_date = $request->received_date;

            if($request->form_type == 1){
                // cash receive form
                $declaration_text = "I hereby acknowledge that I received the above mentioned amount by ".$payment_method." I am responsible to pay the amount.
                If I unable to pay the mentioned amount, The Authority Reserved Rights to take any legal actions.";
                $form_title = "CASH RECEIVED FORM";

                $amount = $request->amount;
                $in_word = (new HelperController())->numberToWord($amount);
                $project_name = is_null($request->project_id) ? "" : (new ProjectDataService())->getProjectNameByProjectId($request->project_id);
                $company = (new CompanyDataService())->findCompanryProfile();
                $prepared_by = Auth::user()->name;
                return view('admin.download_form.cash_received_form',compact('employee','company','amount','in_word','prepared_by','payment_method','receiver_type','received_date','remarks','declaration_text','form_title','project_name'));


            }else if($request->form_type == 2){
                // cash receipt
                $declaration_text =  "";
                $form_title = "CASH RECEIPT FORM";
                $amount = $request->amount;
                $in_word = (new HelperController())->numberToWord($amount);

                $company = (new CompanyDataService())->findCompanryProfile();
                $prepared_by = Auth::user()->name;
                $received_date = date('F jS, Y', strtotime($received_date)) ;
                return view('admin.download_form.cash_receipt_form',compact('employee','company','amount','in_word','prepared_by','payment_method','receiver_type','received_date','remarks','declaration_text','form_title'));

            }


        }
         elseif($request->request_type == 3){
            // Expense Invoice form

            return $this->createExpenseInvoiceFormPDF($request);
        }
        else {
            // multiple employee advance form for food purpose from advance report page

                  try{



                    $project_id = $request->proj_name;
                    $buildingId = $request->acomdOfbId;
                    $adv_amount = $request->adv_amount;
                    $adv_date = strtotime($request->adv_date) ? date('d/m/Y', strtotime($request->adv_date)) : '';
                    $remarks = $request->remarks;
                    //
                    if (is_null($request->adv_emp_ids) == false) {
                        $allEmplId = explode(",", $request->adv_emp_ids);
                        $allEmplId = array_unique($allEmplId); // remove multiple same empl ID
                        $records = (new EmployeeDataService())->getMultipleEmpIDWiseInformationWithSalaryDetailForEmployeeAdvancePaper($allEmplId);
                        $project_name = "";
                    } else {
                        $records = (new EmployeeDataService())->getEmployeesInfoWithSalaryDetailForEmployeeAdvancePaper($project_id, $buildingId);
                        $project_name = (new EmployeeRelatedDataService())->getProjectNameByProjectId($project_id);
                    }
                    // Calculate previous amount (unpaid advances) for each employee
                    foreach ($records as $record) {
                        $total_given = (new EmployeeAdvanceDataService())->getAnEmployeeOthersAdvanceTotalAmount($record->emp_auto_id, null, 0);
                        $total_deducted = (new SalaryProcessDataService())->getTotalAmountOfOtherAdvanceDeductionFromSalary($record->emp_auto_id, true);
                        $record->previous_amount = $total_given - $total_deducted;
                    }

                    $amount_in_word = (new HelperController())->numberToWord($adv_amount*count($records));
                    $company = (new CompanyDataService())->findCompanryProfile();
                    $prepared_by = Auth::user()->name;
                    return view('admin.download_form.multi_emp_advance_paper', compact('records','amount_in_word', 'company', 'project_name', 'adv_amount', 'adv_date', 'prepared_by', 'remarks'));


                }catch(\Exception $e){
                    return $e->getMessage();
                }

        }


    }


        public function createExpenseInvoiceFormPDF($request){



            if(is_null($request->employee_id)){
                    return "Please Input the Employee ID or Employee Name and Try Again";
            }
             $employee = null;
            if(is_numeric($request->employee_id)){
                $employee = (new EmployeeDataService())->searchAnEmployeeWithImportantInformationAsShowByMultitypeParameter($request->employee_id,'employee_id',Auth::user()->branch_office_id);
                 if(!$employee){
                    return "Please Input the Employee ID or Employee Name and Try Again";
                }
            }else{
                $employee = new stdClass();
                $employee->employee_id = "";
                $employee->employee_name = $request->employee_id;
                $employee->catg_name = "";
                $employee->akama_no = "";
                $employee->employee_id = "";
                $employee->joining_date = "";
                $employee->mobile_no  = "";
                $employee->address = "";


            }


        $remarks = $request->remarks;
        $issue_date = $request->issue_date;
        $invoice_no =   $request->invoice_no;
        $received_date = $request->received_date;
        $current_datetime = now()->format('d M Y h:i A');
        $amount = $request->amount;
        $in_word = (new HelperController())->numberToWord($amount);
        $project_name = is_null($request->project_id) ? "" : (new ProjectDataService())->getProjectNameByProjectId($request->project_id);
        $company = (new CompanyDataService())->findCompanryProfile();
        $prepared_by = Auth::user()->name;

        $pdf = Pdf::loadView('admin.download_form.expense_pdf', [
            'company' => $company,
            'current_datetime' => $current_datetime,
            'employee' => $employee,
            'amount' => $amount,
            'in_word' => $in_word,
            'prepared_by' => $prepared_by,
            'received_date' => $received_date,
            'remarks' => $remarks,
            'project_name' => $project_name,
             'issue_date' => $issue_date,
            'invoice_no' => $invoice_no,

        ])->setPaper('a4');

        //! Open PDF in browser
        return $pdf->stream('expense.pdf');
    }

}
