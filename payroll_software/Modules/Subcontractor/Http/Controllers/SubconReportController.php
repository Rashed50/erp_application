<?php

namespace Modules\Subcontractor\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Admin\Helper\HelperController;
use App\Http\Controllers\DataServices\{CompanyDataService,ProjectDataService,EmployeeRelatedDataService,SalaryProcessDataService};
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\Log;
use Modules\Subcontractor\Services\SubcontractorDataService;
use carbon\Carbon;

//use App\Models\ProjectInfo;

class SubconReportController extends Controller
{
    public function index()
    {


        $projects = (new ProjectDataService())->getAllActiveProjectListForDropdown(1);
        $subcontractors = (new SubcontractorDataService())->getAllActiveSubcontractorsForDropdownList();


        //   dd($subcontractors);
        $status = [
            ['id' => 1,  'name' => 'Active'],
            ['id' => 0,  'name' => 'Inactive'],
        ];
        return view('subcontractor::pages.report.index', compact('projects', 'status', 'subcontractors'));
    }

    public function salesReportListAPI(Request $request)
    {
        $query = ChartofaccSalesRecord::with([
            'customer',
            'items',
            'debit',
            'credit',
            'ProjectDetails',
        ]);

        // 🔍 Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('sr_invoice_no', 'like', "%{$search}%")
                    ->orWhere('sr_invoice_description', 'like', "%{$search}%")
                    ->orWhere('sr_payment_terms', 'like', "%{$search}%")
                    ->orWhere('sr_payment_mean', 'like', "%{$search}%");
            });
        }


        // 🔹 Filters
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('sr_status')) {
            $query->where('sr_status', $request->sr_status);
        }

        if ($request->filled('due_date_from')) {
            $query->whereDate('sr_due_date', '>=', $request->due_date_from);
        }

        if ($request->filled('due_date_to')) {
            $query->whereDate('sr_due_date', '<=', $request->due_date_to);
        }

        // 🔢 Per Page (default 10)
        $perPage = $request->get('per_page', 10);

        // 📄 Pagination
        // $records = $query->orderBy('sr_auto_id', 'desc')->paginate($perPage);
        $records = (clone $query)->orderBy('sr_auto_id', 'desc')->paginate($perPage);

        // 🔹 Footer totals calculation
        $totals = [
            'total_amount'      => (clone $query)->sum('sr_total_amount'),
            'total_vat'         => (clone $query)->sum('sr_vat_amount'),
            'total_grand'       => (clone $query)->sum('sr_grand_total_amount'),
            'retention_amount'  => (clone $query)->sum('retention_amount'),
            'receivable_amount' => (clone $query)->sum('sr_grand_total_amount') - (clone $query)->sum('retention_amount'),
        ];

        return response()->json([
            'records' => $records,
            'totals'  => $totals,
        ]);
    }


    public function searchSubcontractorForView($request)
    {
         $from_date = $request->has('from_date') ? $request->from_date : null;
        //  $year = (int) $request->year;
        $project_id_array =  $request->project_id != null ?  $request->project_id : (new ProjectDataService())->getAllActiveProjectIDOfABranchOfficeAsArray(Auth::user()->branch_office_id);
        $sponsor_id_array = (new SubcontractorDataService())->getAllActiveSubcontractorsSponsorIdAsArray();

        $day_month_year = (new HelperController())->getDayMonthAndYearFromDateValue($from_date);
        $sponsors = (new EmployeeRelatedDataService())->getListOfActiveSponserInfoOrderBySponsorIdAndByMultipleId($sponsor_id_array);
        // dd($day_month_year  );
        $summary_records = array();
        $counter = 0;
        $month = (int)$day_month_year[1]; // day month year
        $year = (int)$day_month_year[2];
        //  dd($project_id_array  );
        foreach ($project_id_array as $p) {

            $aproject = (new ProjectDataService())->findAProjectInformationWithSelectedBasicInformation($p);
            $sp_records = array();
            $sp_counter = 0;
            $is_data_found = false;
            foreach ($sponsors as $sp) {
                // spons_id
                $records = (new SalaryProcessDataService())->getAProjectWorkedEmployeeSalaryForSelectedSponsorsSummaryReport($p, $sp->spons_id, $month, $year);
                if ($records) {
                    $sp_records[$sp_counter] = $records;
                    $is_data_found = true;
                } else {
                    $object = new \stdClass();
                    $object->sponsor_id = $sp->spons_id;
                    $object->total_emp = 0;
                    $object->total_hours = 0;
                    $object->total_gross_salary = 0;
                    $sp_records[$sp_counter] =  $object;
                }
                $sp_counter += 1;
            }
            if ($is_data_found) {
                $aproject->salary_records = $sp_records;
                $summary_records[$counter++] = $aproject;
            }
        }

        $payments = (new SubcontractorDataService())->getMultipleSubcontractorPaymentReport($month, $year);
        // dd(@$sponsors, $summary_records);
        $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
        $login_name = Auth::user()->name;

        $company = (new CompanyDataService())->findCompanryProfile();
        $current_datetime = now()->format('d M Y h:i A');

        $pdf = pdf::loadView('subcontractor::pages.report.single_month_service_payment', [
            'summary_records' => $summary_records,
            'sponsors' => $sponsors,
            //'totals'  => $totals,
            'filters' => $request->all(),
            'payments' => $payments,
            'month' => $month,
            'year' => $year,
            'company' => $company,
            'current_datetime' => $current_datetime,
            'login_name' => $login_name,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('single_month_service_payment.pdf');


        // }catch(Exception $ex){
        //      "System Data Processsing Error ".$ex;
        // }

        return response()->json($record);
    }


    public function getSubcontractorWorkingEmployeesForAMonth($request)
    {
        try{

                $subcontractors =  (new SubcontractorDataService())->getAllSubcontractors();

                // dd($day_month_year  );
                $summary_records = array();
                $counter = 0;
                // //  dd($project_id_array  );
                foreach ($subcontractors as $sc) {

                    // spons_id
                   $total_emp = (new SubcontractorDataService())->countTotalNumberOfCurrentlyActiveEmployeesInASubcontractorForReport($sc->sponsor_id);
                    // $record = (new SubcontractorDataService())->getASubcontractorManpowerWorkedDetailsInAMonthForReport($sc->sponsor_id, $month, $year);
                    // if (!$record) {
                    //     $record = new \stdClass();
                    //     $record->total_emp = 0;
                    //     $record->total_hours = 0;
                    //     $record->total_salary = 0;
                    // }
                    $sc->total_emp = $total_emp;
                    $sc->payment_record = (new SubcontractorDataService())->getASubcontractorLastPaymentPaymentRecord($sc->subcon_auto_id);
                }


                $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
                $login_name = Auth::user()->name;

               // dd($subcontractors);

                $company = (new CompanyDataService())->findCompanryProfile();
                $current_datetime = now()->format('d M Y h:i A');

                return view('subcontractor::pages.report.active_manpower_report', [
                    'subcontractors' => $subcontractors,
                    'company' => $company,
                    'current_datetime' => $current_datetime,
                    'login_name' => $login_name,
                ]);



         }catch(Exception $ex){
            return "System Data Processsing Error ".$ex;
        }


    }



    public function SingleMonthServiceAndPaymentDownloadPDF(Request $request)
    {
       // dd($request->all());
        if ($request->reportType == '5') {
            // summary report
            return $this->processSubcontractorDetailsSummary($request->subcontractor_id);
        }

        elseif ($request->reportType == '4') {
            // single report
            //   dd($request->all());
            return $this->processSingleSubcontractorDetailsSummaryUptoSelectedDate($request->subcontractor_id, $request->from_date, $request->to_date);
        }
        // Report 3
        elseif ($request->reportType == '3') {
            // outstanding balance report
            return $this->processASubcontractorServiceAndPaymentSummaryStatement($request);
        }

        elseif ($request->reportType == '6') {
            // single month payment details
            return $this->processManpowerSupplierMonthlyPaymentReport($request);
        }
         elseif ($request->reportType == '7') {
            return $this->getSubcontractorWorkingEmployeesForAMonth($request);
        }
        elseif ($request->reportType == '8') {
            // return "Subcontracts Details Report";
            return view('subcontractor::pages.report.subcontractor_details_report', [
                'subcontractors' => (new SubcontractorDataService())->getAllSubcontractors(),
                'company' => (new CompanyDataService())->findCompanryProfile(),

                 'login_name' => Auth::user()->name,
            ]);
        }
        elseif ($request->reportType == '1') {
            // MULTIPLE SUB-CONTRACTORS SINGLE MONTH WORKED SUMMARY REPORT
            return $this->processMonthtlyMultiSponsorWorkedSummaryReport($request);

        }
        elseif ($request->reportType == '2') {
            // multi sponsor project base salary summary report
            return $this->processSingleMonthMultiSponsorWorkingProjectBaseSalarySummaryReport($request);
        }

        else {
            return "Invalid Report Type";
        }


    }

    private function getPaymentMethodText($methodCode)
    {
        $methods = [
            '1' => 'Cash',
            '2' => 'Bank Transfer',
            // '3' => 'Cheque',
            // '4' => 'Online Transfer'
        ];

        return $methods[$methodCode] ?? 'Cash';
    }

    // 3
       private function processASubcontractorServiceAndPaymentSummaryStatement(Request $request){

        try{

            $from_date = $request->from_date;
            $to_date = $request->to_date;
            $subcontractor_id = $request->subcontractor_id;

            $day_month_year  = (new HelperController())->getDayMonthAndYearFromDateValue($from_date);
            $day = $day_month_year[0] ;
            $month =  $day_month_year[1];
            $year =  $day_month_year[2];
            $date = Carbon::parse($from_date);
            $previous_month_last_date = $date->subMonth()->endOfMonth()->toDateString();
            $subcontractor_service = new SubcontractorDataService();

            // subcontractor information
            $subcontract_info = $subcontractor_service->getASubContractorInfoForReport($subcontractor_id);
            if(!$subcontract_info){
                return "Subcontractor Not Found";
            }

            $service_invoice_total_amount = 0;// (new SubcontractorDataService())->calculateASubcontractSingleMonthTotalInvoiceAmount($subcontractor_id,$month, $year);        // dd($records);
            $service_records = (new SubcontractorDataService())->searchASubcontractorFromDateToDateServiceRecordsOrderByInvoiceDate($subcontractor_id,$from_date, $to_date);        // dd($records);

            $total_paid_amount = 0 ;// (new SubcontractorDataService())->getASubcontractorSinlgeMonthTotalPaidAmount($subcontractor_id, $month, $year);
            $payment_records = (new SubcontractorDataService())->searchASubcontractorFromDateToDatePaymentRecordsOrderByPaymentDate($subcontractor_id, $from_date, $to_date);
            
            $upto_last_month_balance = (new SubcontractorDataService())->processASubcontractorUptoPreviousMonthRemainingBalance($subcontractor_id, $previous_month_last_date);

            // Format service records

            $service_invoice_total_amount = $service_records->reduce(function ($carry, $record) {
                return $carry + $record->grand_total;
            }, 0);

            $total_paid_amount = $payment_records->reduce(function ($carry, $record) {
                return $carry + $record->grand_total;
            }, 0);
             
            $formatted_service = $service_records->map(function ($record) {
                 
                return [
                    'date' => $record->invoice_date,
                    'type' => 'SERVICE',
                    'description' => $record->remarks ?? 'Service Invoice',
                    'invoice_no' => $record->invoice_no,
                    'amount' => $record->grand_total,
                    'balance' => null,
                    'record' => $record // Keep original object

                ];
            });

            // Format payment records
            $formatted_payment = $payment_records->map(function ($record) {
                 
                return [
                    'date' => $record->payment_date,
                    'type' => 'PAYMENT',
                    'description' => $record->remarks ?? 'Payment Received',
                    'invoice_no' => $record->invoice_no ?? '-',
                    'amount' => $record->grand_total,
                    'balance' => null,
                    'record' => $record
                ];
            });

            // Merge and sort
            $final_records = $formatted_service->merge($formatted_payment)
                ->sortBy('date')
                ->values();

            $subcontract_info->final_records = $final_records;
            $subcontract_info->service_invoice_total_amount = $service_invoice_total_amount;
            $subcontract_info->total_paid_amount = $total_paid_amount;
            $subcontract_info->upto_last_month_balance = $upto_last_month_balance;



            $company = (new CompanyDataService())->findCompanryProfile();
            $current_datetime = now()->format('d M Y');
            $login_name = Auth::user()->name;
           // dd($subcontract_info);


            $data = [
                'subcontractor_info'       => $subcontract_info,
                'company'                 => $company,
                'current_datetime'        => $current_datetime,
                'login_name'              => $login_name,
                'month_name'            => strtoupper(\Carbon\Carbon::parse($from_date)->format('F')),
                'month' =>$month,
                'year' =>$year,
                'from_date'=>$from_date,
                'to_date'=>$to_date
                // 'amount_in_words'         => $amount_in_words
            ];

           // return view('subcontractor::pages.report.subcon_month_summary_new_report', $data);
            $pdf = pdf::loadView('subcontractor::pages.report.subcon_month_summary_new_report',$data);
            return $pdf->stream('supplyer_report_' . date('Y-m-d_H-i-s') . '.pdf');

        }
        catch(Exception $ex) {
             return "System Data Processing Error: " . $ex->getMessage();
        }

    }


    //5 subcontractor summary report
    private function processSubcontractorDetailsSummary($subcontract_id)
    {
        try{

                if(empty($subcontract_id)){
                    return "Select a Subcontractor and Try Again";
                }
                $subcontractor_service = new SubcontractorDataService();
                // subcontractor information
                $subcontract_info = $subcontractor_service->getASubContractorInfoForReport($subcontract_id);
                // payment summary
                $total_invoice_amount = $subcontractor_service->subContractorTotalInvoiceAmount($subcontract_id);
                $total_paid_amount = $subcontractor_service->subContractorTotalPaidAmount($subcontract_id);
                $total_payment_transaction = $subcontractor_service->subContractorTotalPaymentTransaction($subcontract_id);
                // company profile
                $company = (new CompanyDataService())->findCompanryProfile();

                $data = [
                    'company'                       => $company,
                    'subcontract_info'              => $subcontract_info,
                    'total_invoice_amount'          => $total_invoice_amount,
                    'total_paid_amount'             => $total_paid_amount,
                    'total_payment_transaction'     => $total_payment_transaction,
                ];

                return view('subcontractor::pages.report.subcontract_overall_summary', $data);
                // $pdf = Pdf::loadView('subcontractor::pages.report.subcontract_overall_summary', $data)
                //     ->setPaper('a4', 'portrait');

                // return $pdf->stream('subcontractor_payment_statement.pdf');
        }
        catch(Exception $ex) {
             return "System Data Processing Error: " . $ex->getMessage();
        }

        
    }

    // 4 Single subcontractor monthly worked manpower details
    private function processSingleSubcontractorDetailsSummaryUptoSelectedDate($subcontract_id, $start_date, $end_date)
    {
        try{
             $day_month_year = (new HelperController())->getDayMonthAndYearFromDateValue($start_date);
             $date = Carbon::parse($start_date);
            $previous_month_last_date = $date->subMonth()->endOfMonth()->toDateString();

            $subcontractor_service = new SubcontractorDataService();

            // subcontractor information
            $subcontract_info = $subcontractor_service->getASubContractorInfoForReport($subcontract_id);
            if(!$subcontract_info){
                return "No Subcontractor Found";
            }

            $service_records = (new SubcontractorDataService())->processASubcontractorSinlgeMonthManpowerSummaryGroupByProject($subcontract_id, $day_month_year[1], $day_month_year[2]);        // dd($records);
            $payment_records = (new SubcontractorDataService())->processASubcontractorSinlgeMonthPaymentRecords($subcontract_id, $day_month_year[1], $day_month_year[2]);
            $upto_last_month_balance = (new SubcontractorDataService())->processASubcontractorUptoPreviousMonthRemainingBalance($subcontract_id, $previous_month_last_date);
            $subcontract_info->service_records = $service_records;
            $subcontract_info->payment_records = $payment_records;
            $subcontract_info->upto_last_month_balance = $upto_last_month_balance;

            $company = (new CompanyDataService())->findCompanryProfile();
            $current_datetime = now()->format('d M Y');
            $login_name = Auth::user()->name;

            $data = [
                'subcontractor_info'       => $subcontract_info,
                'company'                 => $company,
                'current_datetime'        => $current_datetime,
                'login_name'              => $login_name,
                'report_month'            => strtoupper(\Carbon\Carbon::parse($start_date)->format('F Y')),
                'month' =>$day_month_year[1],
                'year' =>$day_month_year[2],
                // 'amount_in_words'         => $amount_in_words
            ];


            return View('subcontractor::pages.report.single_month_summary', $data);
            $pdf = Pdf::loadView('subcontractor::pages.report.single_month_summary', $data)
                ->setPaper('a4', 'portrait');

            return $pdf->stream('subcontractor_payment_statement.pdf');
        }
        catch(Exception $ex) {
             return "System Data Processing Error: " . $ex->getMessage();
        }
    }

     // 6 single month payment details
    private function processManpowerSupplierMonthlyPaymentReport(Request $request)
    {
        try {

           
            $from_date = $request->from_date ?? now()->startOfMonth()->toDateString();
            $day_month_year = (new HelperController())->getDayMonthAndYearFromDateValue($from_date);
            $month          = (int) $day_month_year[1];
            $year           = (int) $day_month_year[2];

            // Last day of the previous month

            $date = Carbon::parse($from_date);
            $prev_end = $date->subMonth()->endOfMonth()->toDateString();

            if($request->has('subcontractor_id') && !empty($request->subcontractor_id)){
                $subcon_auto_ids = [$request->subcontractor_id];
            } else {
                $subcon_auto_ids = (new SubcontractorDataService())->getAllActiveSubcontractorsAutoIdAsArray();
            }
            $records = (new SubcontractorDataService())->getManpowerMonthlyPaymentData($subcon_auto_ids, $month, $year, $prev_end);

            $company          = (new CompanyDataService())->findCompanryProfile();
            $current_datetime = now()->format('d-m-y');
            $login_name       = Auth::user()->name;

            return view('subcontractor::pages.report.month_base_service_and_payment', [
                'records'    => $records,
                'month'      => $month,
                'year'       => $year,
                'month_name' => \Carbon\Carbon::parse($from_date)->format('F'),
                'print_date' => $current_datetime,
                'login_name' => $login_name,
                'company'    => $company,
            ]);
        } catch (Exception $ex) {
            return 'System Data Processing Error: ' . $ex->getMessage();
        }
    }

 

      //2 sposnor/sub-contractor: multi sponsor project base salary summary
    private function processSingleMonthMultiSponsorWorkingProjectBaseSalarySummaryReport($request){
        try{


         //   dd($request->all());


            $day_month_year = (new HelperController())->getDayMonthAndYearFromDateValue($request->from_date);
            $month          = (int) $day_month_year[1];
            $year           = (int) $day_month_year[2];

            $project_id_array =  $request->project_id != null ? [$request->project_id] :  (new ProjectDataService())->getAllActiveProjectIDOfABranchOfficeAsArray(Auth::user()->branch_office_id);
            $sponsor_id_array = (new SubcontractorDataService())->getAllActiveSubcontractorsSponsorIdAsArray();

            // $request->subcontractor_id != null ? [$request->subcontractor_id] : (new SubcontractorDataService())->getAllActiveSubcontractorsSponsorIdAsArray();
            // if ( is_null($months) ||  count($months) > 1  ) {
            //     return "Please Select Only One Month ";
            // }
            // else if(  count($sponsor_id_array) > 6 ||  count($sponsor_id_array) < 1  ){
            //     return "  Please Select Maximum 6 Sponsors";
            // }


            $sponsors = (new EmployeeRelatedDataService())->getListOfActiveSponserInfoOrderBySponsorIdAndByMultipleId( $sponsor_id_array);
           // dd($sponsor_id_array);
       //     dd($sponsors);
            $summary_records = array();
            $counter = 0;

            foreach($project_id_array as $p){

                $aproject = (new ProjectDataService())->findAProjectInformationWithSelectedBasicInformation($p);
                $sp_records = array();
                $sp_counter = 0;
                $is_data_found = false;
                foreach($sponsors as $sp){

                    $records = (new SalaryProcessDataService())->getAProjectWorkedEmployeeSalaryForSelectedSponsorsSummaryReport($p, $sp->spons_id ,$month,$year);

                    if($records) {
                        $sp_records[$sp_counter] = $records;
                        $is_data_found = true;
                    }else {
                        $object = new \stdClass();
                        $object->sponsor_id = $sp->spons_id;
                        $object->total_emp = 0;
                        $object->total_hours = 0;
                        $object->total_gross_salary = 0;
                        $sp_records[$sp_counter] =  $object;
                    }
                    $sp_counter += 1;
                }
                if($is_data_found){
                    $aproject->salary_records = $sp_records;
                    $summary_records[$counter++] = $aproject;
                }


            }
         //   dd($sponsors, $summary_records);
            $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
            $login_name = Auth::user()->name;
            $company = (new CompanyDataService())->findCompanryProfile();
            $current_datetime = now()->format('d M Y');
            $login_name = Auth::user()->name;

            $data = [
                'summary_records'       => $summary_records,
                'sponsors'              => $sponsors,
                'company'                 => $company,
                'current_datetime'        => $current_datetime,
                'login_name'              => $login_name,
                'report_month'            => $month,
                'month' =>$day_month_year[1],
                'year' =>$day_month_year[2],
            ];

            return view('subcontractor::pages.report.amonth_project_base_salary_summary', $data);

           // return view('subcontractor::pages.report.amonth_project_base_salary_summary', compact('summary_records','sponsors','month', 'year', 'company' ,'login_name'));

        }catch(Exception $ex){
            return "System Data Processsing Error ".$ex;
        }
    }

    private function processMonthtlyMultiSponsorWorkedSummaryReport($request){
        try{


         //   dd($request->all());


            $day_month_year = (new HelperController())->getDayMonthAndYearFromDateValue($request->from_date);
            $month          = (int) $day_month_year[1];
            $year           = (int) $day_month_year[2];

            $project_id_array =  $request->project_id != null ? [$request->project_id] :  (new ProjectDataService())->getAllActiveProjectIDOfABranchOfficeAsArray(Auth::user()->branch_office_id);
            $sponsor_id_array = (new SubcontractorDataService())->getAllActiveSubcontractorsSponsorIdAsArray();

            // $request->subcontractor_id != null ? [$request->subcontractor_id] : (new SubcontractorDataService())->getAllActiveSubcontractorsSponsorIdAsArray();
            // if ( is_null($months) ||  count($months) > 1  ) {
            //     return "Please Select Only One Month ";
            // }
            // else if(  count($sponsor_id_array) > 6 ||  count($sponsor_id_array) < 1  ){
            //     return "  Please Select Maximum 6 Sponsors";
            // }


            $summary_records =   (new SubcontractorDataService())->processSubcontractorsSinlgeMonthWorkSummaryReport($sponsor_id_array, $month, $year);

          //  dd($summary_records);
            $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
            $login_name = Auth::user()->name;
            $company = (new CompanyDataService())->findCompanryProfile();
            $current_datetime = now()->format('d M Y');
            $login_name = Auth::user()->name;

            $data = [
                'summary_records'       => $summary_records,
                'company'                 => $company,
                'current_datetime'        => $current_datetime,
                'login_name'              => $login_name,
                'report_month'            => $month,
                'month' =>$day_month_year[1],
                'year' =>$day_month_year[2],
            ];

            return view('subcontractor::pages.report.amonth_subcon_work_summary', $data);


        }catch(Exception $ex){
            return "System Data Processsing Error ".$ex;
        }
    }




}
