<?php

namespace Modules\Subcontractor\Services;

// use Entities\Models\SubcontractorInfo;
use Modules\Subcontractor\Entities\SubcontractorInfo;
use Modules\Subcontractor\Entities\SubcontractorService;
use Modules\Subcontractor\Entities\SubcontractorPayment;
use App\Models\{SalaryHistory, EmployeeInfo, EmployeeMultiProjectWorkHistory};
use App\Http\Controllers\Admin\Helper\HelperController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Illuminate\Http\Request;

class SubcontractorDataService
{
    /**
     * Validate subcontractor data.
     */
    public function validateData(array $data)
    {
        return Validator::make($data, [
            'subcon_name'    => 'required|string|max:150',
            'passfort_no'    => 'required|string|max:20',
            'pass_expire'    => 'required|date',
            'iqama_no'       => 'required|string|max:15',
            'iqama_expire'   => 'required|date',
            'mobile_no'      => 'required|string|max:13',
            'abshar_mobile_no'   => 'nullable|string|max:13',
            'country_contact_no' => 'nullable|string|max:13',
            'country_id'      => 'integer',
            'division_id'     => 'integer',
            //  'district_id'   => 'nullable|integer',
            //  'post_code'     => 'nullable|string|max:20',
            'details'         => 'nullable|string|max:255',
            'present_address' => 'nullable|string|max:255',
            'subcont_email'   => 'nullable|email|max:50',
            'joining_date'    => 'nullable|date',
            // 'entry_date'    => 'nullable|date',
            'remarks'         => 'nullable|string|max:255',
            // 'iqama_file'   => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:12048',
            // 'passport_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:12048',
            // 'contract_paper' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:12048',
        ]);
    }

    public function UpdateValidateData(array $data)
    {
        return Validator::make($data, [
            'subcon_name'        => 'required|string|max:150',
            // 'sponsor_id'        => 'required|integer|exists:sponsors,spons_id',
            'passfort_no'        => 'required|string|max:20',
            'pass_expire'        => 'required|date',
            'iqama_no'           => 'required|string|max:15',
            'iqama_expire'       => 'required|date',
            'mobile_no'          => 'required|string|max:13',
            'abshar_mobile_no'   => 'nullable|string|max:13',
            'country_contact_no' => 'nullable|string|max:13',
            'country_id'         => 'required|integer|exists:countries,id',
            'division_id'        => 'required|integer|exists:divisions,division_id',
            'details'            => 'nullable|string|max:255',
            'joining_date'       => 'nullable|date',
            'entry_date'         => 'nullable|date',
        ]);
    }

    public function prepareUpdateData(array $data)
    {
        return [

            'subcon_name'        => $data['subcon_name'],
            'id_number'        => $data['id_number'],
            // 'sponsor_id'        => $data['sponsor_id'],
            'passfort_no'        => $data['passfort_no'],
            'pass_expire'        => $data['pass_expire'],
            'iqama_no'           => $data['iqama_no'],
            'iqama_expire'       => $data['iqama_expire'],
            'mobile_no'          => $data['mobile_no'],
            'country_id'         => $data['country_id'],
            'division_id'        => $data['division_id'],
            'details'            => $data['details'] ?? null,
            'joining_date'       => $data['joining_date'] ?? null,
            'opening_balance'   => $data['opening_balance'] ?? 0,
            'entry_date'         => $data['entry_date'] ?? null,
            'abshar_mobile_no'   => $data['abshar_mobile_no'] ?? null,
            'country_contact_no' => $data['country_contact_no'] ?? null,
        ];
    }

    /**
     * Handle file uploads.
     */
    public function handleFileUpload(Request $request, $field)
    {
        if ($request->hasFile($field)) {
            return $request->file($field)->store('documents', 'public');
        }
        return null;
    }

    public static function uploadSingleFile(string $dir, $file)
    {

        $fileName = Carbon::now()->toDateString() . "-" . uniqid() . "." . $file->getClientOriginalExtension();

        if (!Storage::disk('public')->exists($dir)) {
            Storage::disk('public')->makeDirectory($dir);
        }

        return $file->storeAs($dir, $fileName, 'public');
    }

    public static function handleSingleFileUpload(string $dir, $files = [])
    {
        $uploadedFiles = [];
        $counter = 0;

        foreach ($files as $file) {
            $fileName = Carbon::now()->toDateString() . "-" . uniqid() . "." . $file->getClientOriginalExtension();

            if (!Storage::disk('public')->exists($dir)) {
                Storage::disk('public')->makeDirectory($dir);
            }

            return  $filePath = $file->storeAs($dir, $fileName, 'public');
            $uploadedFiles[$counter++] = $filePath;
        }

        return count($uploadedFiles) === 1 ? $uploadedFiles[0] : $uploadedFiles;
    }

    /**
     * Store subcontractor in the database.
     */
    public function storeSubcontractor(array $data)
    {
        return SubcontractorInfo::create($data);
    }

    /**
     * Update subcontractor in the database.
     */
    public function updateSubcontractor(SubcontractorInfo $subcontractor, array $data)
    {
        $subcontractor->update($data);
        return $subcontractor;
    }

    /**
     * Delete a subcontractor.
     */
    public function deleteSubcontractor(SubcontractorInfo $subcontractor)
    {
        return $subcontractor->delete();
    }

    /**
     * Get all subcontractors.
     */
    public function getAllSubcontractors()
    {
        return SubcontractorInfo::all();
    }
    public function getASubcontractorById($id)
    {
        return SubcontractorInfo::where('subcon_auto_id', $id)->first();
    }

    public function getASubContractorInfoForReport($subcontract_id)
    {
        return DB::table('subcontractor_infos')
            ->where('subcon_auto_id', $subcontract_id)
            ->select('subcon_auto_id', 'subcon_name', 'passfort_no', 'iqama_no', 'mobile_no', 'opening_balance')
            ->first();
    }
    public function getListOfActiveSubcontractors()
    {
        return SubcontractorInfo::where('act_status', 1)->get();
    }

    public function getAllActiveSubcontractorsForDropdownList()
    {
        return SubcontractorInfo::select('subcon_auto_id', 'subcon_name')
            ->where('act_status', 1)
            ->get();
    }
    public function getAllActiveSubcontractorsAutoIdAsArray()
    {
        return SubcontractorInfo::select('subcon_auto_id')
            ->where('act_status', 1)
            ->get()->pluck('subcon_auto_id')->toArray();
    }
    public function getAllActiveSubcontractorsSponsorIdAsArray()
    {
        return SubcontractorInfo::select('sponsor_id')
            ->where('act_status', 1)
            ->get()->pluck('sponsor_id')->toArray();
    }

    public function searchSubcontractors($searching_value)
    {
        return SubcontractorInfo::select('sponsors.spons_name', 'subcontractor_infos.*')->where('subcon_name', 'LIKE', "%{$searching_value}%")
            ->orWhere('remarks', 'LIKE', "%{$searching_value}%")
            ->leftjoin('sponsors', 'sponsors.spons_id', '=', 'subcontractor_infos.sponsor_id')
            ->orderBy('subcontractor_infos.subcon_auto_id', 'desc')
            ->get();
    }

    /**
     * Get a single subcontractor by ID.
     */
    public function getSubcontractorById($id)
    {
        return SubcontractorInfo::where('subcon_auto_id', $id)->first();
    }


    /**
     * Calculate total amount based on units, rate, and discount
     */
    public function calculateTotalAmount($no_of_unit, $per_unit_rate)
    {
        $total = $no_of_unit * $per_unit_rate;
        // $total -= ($total * $discount) / 100; // Apply discount if exists
        return round($total, 2);
    }
    public function findASubcontractorServiceRecord($subcon_auto_id, $month, $year, $service_type)
    {
        return SubcontractorService::where('subcon_auto_id', $subcon_auto_id)
            ->where('month', $month)
            ->where('year', $year)
            ->where('service_type', $service_type)
            ->first();
    }

    /**
     * Store a new subcontractor service
     */
    public function storeSubcontractorService($data)
    {

        // Insert into database
        return SubcontractorService::create([
            'subcon_auto_id' => $data['subcon_auto_id'],
            'no_of_unit' => $data['no_of_unit'],
            'per_unit_rate' => $data['per_unit_rate'],
            'total_amount' => $data['total_amount'],
            'discount' => $data['discount'],
            'grand_total' => $data['grand_total'],  // Assuming ground_total is the same as total_amount
            'month' => $data['month'],
            'year' => $data['year'],
            'service_type' => $data['service_type'],
            'invoice_date' =>  $data['invoice_date'],
            'invoice_no'  => $data['invoice_no'],
            'service_invoice'  => $data['service_invoice'],
            'remarks' => $data['remarks'],
            'srv_status' => 10, // 10 = aproved, 1 = submitted, 4= review, 6 = waiting approved, 10= approved $data['srv_status'],
            'approved_by' => $data['approved_by'],
            'created_by' => $data['created_by'],
        ]);
    }

    /**
     * Update a subcontractor service
     */
    public function updateSubcontractorService($subcon_service_auto_id, $data)
    {

        // Update the subcontractor service
        return  SubcontractorService::where('subcon_service_auto_id', $subcon_service_auto_id)->update([
            'no_of_unit' => $data['no_of_unit'],
            'per_unit_rate' => $data['per_unit_rate'],
            'total_amount' => $data['total_amount'],
            'discount' => $data['discount'],
            'grand_total' => $data['grand_total'],
            // 'month' => $data['month'],
            // 'year' => $data['year'],
            // 'service_type' => $data['service_type'],
            'remarks' => $data['remarks'],
            'approved_by' => $data['approved_by'],
            'updated_by' => $data['updated_by'],
        ]);
    }

    public function calculateASubcontractSingleMonthTotalInvoiceAmount($subcon_auto_id, $month, $year)
    {

        return SubcontractorService::where('month', $month)
            ->where('year', $year)
            ->where('subcon_auto_id', $subcon_auto_id)
            ->sum('grand_total');
    }

    public function searchServicesForListView($subcon_auto_id, $month, $year)
    {

        return DB::select('call searchASubcontractorServiceForListViewByMonthAndYear(?,?,?)', array($subcon_auto_id, $month, $year));

        // if( $month==null){
        //     return SubcontractorService::where('subcon_auto_id', $subcon_auto_id)
        //    // ->leftjoin('subcontractor_infos')
        //     ->leftjoin('subcontractor_infos', 'subcontractor_services.subcon_auto_id', '=', 'subcontractor_infos.subcon_auto_id')
        //    // ->where('month',$month)
        //     ->where('year',$year)
        //     ->get();
        // }
        // return SubcontractorService::selectwhere('subcon_auto_id', $subcon_auto_id)
        //        // ->where('month',$month)
        //        ->leftjoin('subcontractor_infos', 'subcontractor_services.subcon_auto_id', '=', 'subcontractor_infos.subcon_auto_id')
        //         ->where('year',$year)
        //         ->get();
    }

    public function searchASubcontractorSingleMonthOnlyServiceRecordsOrderByInvoiceDate($subcon_auto_id, $month, $year)
    {

        return SubcontractorService::where('subcon_auto_id', $subcon_auto_id)
            ->where('month', $month)
            ->where('year', $year)
            ->orderBy('invoice_date', 'ASC')
            ->get();
    }
    public function searchASubcontractorFromDateToDateServiceRecordsOrderByInvoiceDate($subcon_auto_id, $from_date, $to_date)
    {

        return SubcontractorService::where('subcon_auto_id', $subcon_auto_id)
                ->whereDate('invoice_date', '>=', $from_date)
                ->whereDate('invoice_date', '<=', $to_date)
                ->orderBy('invoice_date', 'ASC')
                ->get();
    }


    #################################################################################
    ########################## Payment Section ######################################
    #################################################################################


    public function storePayment($data)
    {
        // Calculate total amount before saving
        // $totalAmount = $this->calculateTotalAmount($data['no_of_unit'], $data['per_unit_rate']);
        // ALTER TABLE `subcontractors_payment` ADD `payment_date` DATE NULL DEFAULT NULL AFTER `year`;


        // Insert into database
        return SubcontractorPayment::create([
            'subcon_auto_id' => $data['subcon_auto_id'],
            // 'no_of_unit' => $data['no_of_unit'],
            // 'per_unit_rate' => $data['per_unit_rate'],
            'total_amount' => $data['total_amount'],
            'payment_date' => $data['payment_date'],
            'discount' => $data['discount'],
            'grand_total' => $data['grand_total'],  // Assuming ground_total is the same as total_amount
            'month' => $data['month'],
            'year' => $data['year'],
            'payment_method' => $data['payment_method'],
            'remarks' => $data['remarks'],
            'payment_file' => $data['payment_file'],
            'approved_by' => $data['approved_by'],
            'created_by' => $data['created_by'],
        ]);
    }
    public function checkIsPendingASubcontractPaymentInvoice($subcontract_id)
    {
        $is_pending = SubcontractorPayment::where('subcon_auto_id', $subcontract_id)
            ->where('act_status', 0)
            ->count();
        return $is_pending > 0 ? true : false;
    }

    public function updatePaymentInformation($subcon_pay_auto_id, $data)
    {
        // Insert into database
        return SubcontractorPayment::where('subcon_pay_auto_id', $subcon_pay_auto_id)->update([

            'total_amount' => $data['total_amount'],
            'payment_date' => $data['payment_date'],
            'discount' => $data['discount'],
            'grand_total' => $data['grand_total'],  // Assuming ground_total is the same as total_amount
            'month' => $data['month'],
            'year' => $data['year'],
            'payment_method' => $data['payment_method'],
            'remarks' => $data['remarks'],
            'payment_file' => $data['payment_file'],
            //'approved_by' => $data['approved_by'],
            'updated_by' => $data['updated_by'],
            'payment_type' => $data['payment_type'],
            'act_status' => $data['act_status'],

        ]);
    }

    public function updateAPaymentRecordAsPaidConfirmation($subcon_pay_auto_id, $data)
    {
        // Insert into database
        return SubcontractorPayment::where('subcon_pay_auto_id', $subcon_pay_auto_id)->update([

            'approved_by' => $data['approved_by'],
            'updated_by' => $data['updated_by'],
            'act_status' => $data['act_status'],

        ]);
    }


    // Generate Invoice
    public function generateSubcontractorPaymentInvoice($receive_data)
    {

        // Count how many invoices already exist for this month and year
        $total_existing_invoice_number = SubcontractorPayment::where('month', $receive_data['month'])
            ->where('year', $receive_data['year'])
            ->count();
        // Format all values to two digits
        $countFormatted = str_pad($total_existing_invoice_number + 1, 2, '0', STR_PAD_LEFT);
        $monthFormatted = str_pad($receive_data['month'], 2, '0', STR_PAD_LEFT);
        $yearFormatted = substr($receive_data['year'], -2); // Take last 2 digits of the year

        // Combine into one 6-digit invoice number
        $invoice_data['invoice_no'] = "{$countFormatted}{$monthFormatted}{$yearFormatted}";
        $invoice_data['amount_in_words'] = (new HelperController())->numberToWord($receive_data['total_amount']);
        $invoice_data['month_name'] = (new HelperController())->getMonthName((int)$receive_data['month']);
        $invoice_data['pay_amount'] = $receive_data['total_amount']; // amount that will be paid excluding VAT
        $invoice_data['year'] = $receive_data['year'];
        $invoice_data['vat_amount'] = number_format((($receive_data['total_amount'] * 15) / 100), 2, '.', '');
        $invoice_data['payment_date'] = Carbon::now()->format('d-m-y');
        $invoice_data['payment_method'] = $receive_data['payment_method'];
        $invoice_data['remarks'] = $receive_data['remarks'];

        $invoice_data['subcontractor'] = SubcontractorInfo::where('subcon_auto_id', $receive_data['subcon_auto_id'])
            ->select('subcon_name', 'iqama_no', 'passfort_no', 'mobile_no', 'present_address')
            ->first();

        $invoice_data['total_invoice_amount'] = DB::table('subcontractor_services')
            ->where('subcon_auto_id', $receive_data['subcon_auto_id'])
            ->where('month', $receive_data['month'])
            ->where('year', $receive_data['year'])
            ->sum('grand_total');

        $invoice_data['total_paid'] = SubcontractorPayment::where('subcon_auto_id', $receive_data['subcon_auto_id'])
                 // ->where('act_status',10)
                ->where('month',$receive_data['month'])
                ->where('year',$receive_data['year'])
                ->sum('grand_total');

        return $invoice_data;
    }




    public function searchPaymentRecordsForListView($subcon_auto_id, $month, $year)
    {

        //  return DB::select('call searchASubcontractorServiceForListViewByMonthAndYear(?,?,?)',array($subcon_auto_id,$month,$year));


        $query = SubcontractorPayment::select("users.name", "subcontractor_infos.subcon_auto_id", "subcontractor_infos.subcon_name", "subcontractors_payment.*") //where('subcontractors_payment.subcon_auto_id', $subcon_auto_id)
            ->leftjoin('subcontractor_infos', 'subcontractors_payment.subcon_auto_id', '=', 'subcontractor_infos.subcon_auto_id')
            ->leftjoin('users', 'subcontractors_payment.created_by', '=', 'users.id')
            ->where('year', $year);

        if ($subcon_auto_id) {
            $query->where('subcontractors_payment.subcon_auto_id', $subcon_auto_id);
        }
        if ($month) {
            $query->where('month', $month);
        }
        $query->orderBy('subcontractor_infos.subcon_name', 'asc');
        return $query->get();
    }

    public function getMultipleSubcontractorPaymentReport($month, $year)
    {
        return SubcontractorPayment::select("users.name", "subcontractor_infos.subcon_auto_id", "subcontractor_infos.subcon_name", "subcontractors_payment.*") //where('subcontractors_payment.subcon_auto_id', $subcon_auto_id)
            ->leftjoin('subcontractor_infos', 'subcontractors_payment.subcon_auto_id', '=', 'subcontractor_infos.subcon_auto_id')
            ->leftjoin('users', 'subcontractors_payment.created_by', '=', 'users.id')
            ->where('month', $month)
            ->where('year', $year)
            ->orderBy('subcontractor_infos.subcon_name', 'asc')
            ->get();
    }





    public function PaymentRequestValidate(array $data)
    {
        return Validator::make($data, [
            'subcon_auto_id' => 'required|exists:subcontractor_infos,subcon_auto_id',
            'total_amount'   => 'required|numeric',
            'discount'       => 'required|numeric',
            'grand_total'    => 'required|numeric',
            'month'          => 'required|integer|between:1,12',
            'year'           => 'required|integer|min:2000|max:2100',
            'payment_method' => 'required|in:1,2',
            'remarks'        => 'nullable|string',
            'payment_type' => 'required|string',
            'act_status' => 'required|integer',
            'payment_date' => 'required|date',
            'payment_file'   => 'nullable|file|max:9048', // 9MB max
        ]);
    }





    public function processASubcontractorSinlgeMonthPaymentRecords($subcon_auto_id, $month, $year)
    {
        return SubcontractorPayment::select("users.name", "subcontractor_infos.subcon_auto_id", "subcontractor_infos.subcon_name", "subcontractors_payment.*") //where('subcontractors_payment.subcon_auto_id', $subcon_auto_id)
            ->leftjoin('subcontractor_infos', 'subcontractors_payment.subcon_auto_id', '=', 'subcontractor_infos.subcon_auto_id')
            ->leftjoin('users', 'subcontractors_payment.created_by', '=', 'users.id')
            ->where('month', $month)
            ->where('year', $year)
            ->where('subcontractor_infos.subcon_auto_id', $subcon_auto_id)
            ->get();
    }
    public function searchASubcontractorSinlgeMonthOnlyPaymentRecordsByPaymentDate($subcon_auto_id, $month, $year)
    {
        return SubcontractorPayment::where('month', $month)
            ->where('year', $year)
            ->where('subcon_auto_id', $subcon_auto_id)
            ->orderBy('payment_date', 'ASC')
            ->get();
    }
    public function searchASubcontractorFromDateToDatePaymentRecordsOrderByPaymentDate($subcon_auto_id, $fromDate, $toDate){
       
        $fromDate = Carbon::parse($fromDate);
        $toDate = Carbon::parse($toDate);
        
        $fromYearMonth =  ($fromDate->year * 100) + $fromDate->month;
        $toYearMonth   =  ($toDate->year * 100) + $toDate->month;
        // for example
        // fromYearMonth = 202603
        // toYearMonth   = 202604
        
        return SubcontractorPayment::where('subcon_auto_id', $subcon_auto_id)
            ->whereRaw(
                '(year * 100 + month) BETWEEN ? AND ?',
                [$fromYearMonth, $toYearMonth]
            )
            ->orderBy('payment_date', 'ASC')
            ->get();
    // $fromDate = Carbon::parse($fromDate);
        // $toDate = Carbon::parse($toDate);
    
        // return SubcontractorPayment::where('subcon_auto_id', $subcon_auto_id)
        //     ->where(function($query) use ($fromDate, $toDate) {
        //         $query->where(function($q) use ($fromDate, $toDate) {
        //             // Year range
        //             $q->where('year', '>=', $fromDate->year)
        //             ->where('year', '<=', $toDate->year);
        //         })->orWhere(function($q) use ($fromDate, $toDate) {
        //             // Same year, month range
        //             $q->where('year', $fromDate->year)
        //             ->where('month', '>=', $fromDate->month);
        //         })->orWhere(function($q) use ($fromDate, $toDate) {
        //             // Same year, month range
        //             $q->where('year', $toDate->year)
        //             ->where('month', '<=', $toDate->month);
        //         })->orWhere(function($q) use ($fromDate, $toDate) {
        //             // Between years
        //             $q->where('year', '>', $fromDate->year)
        //             ->where('year', '<', $toDate->year);
        //         });
        //     })
        //     ->orderBy('payment_date', 'ASC')
        //     ->get();

    }


    public function getASubcontractorSinlgeMonthTotalPaidAmount($subcon_auto_id, $month, $year)
    {
        return SubcontractorPayment::where('month', $month)
            ->where('year', $year)
            ->where('subcon_auto_id', $subcon_auto_id)
            ->sum('grand_total');
    }
    public function getASubcontractorLastPaymentPaymentRecord($subcon_auto_id)
    {
        return SubcontractorPayment::where('subcon_auto_id', $subcon_auto_id)
            ->orderBy('payment_date', 'DESC')
            ->first();
    }

    public function processASubcontractorSingleMonthTotalInvoiceAndPaidAmountForInvoiceGeneration($subcon_auto_id,$month,$year)
    {

            //  dd($previous_month_end_date,$subcon_auto_id);
            $total_paid = SubcontractorPayment:://whereIn('act_status',1) // 1 = submitted, 4= review,   10= approved
                where('subcon_auto_id', $subcon_auto_id)
                ->where('month',$month)
                ->where('year',$year)
                ->sum('grand_total');

            $total_invoice_amount = SubcontractorService::where('subcon_auto_id', $subcon_auto_id)
                                ->where('month',$month)
                                ->where('year',$year)
                                ->sum('grand_total');
            return ['total_invoice'=>$total_invoice_amount,'total_payment'=> $total_paid];
    }


    public function processASubcontractorUptoPreviousMonthRemainingBalance($subcon_auto_id, $previous_month_end_date)
    {

        $end_date = Carbon::parse($previous_month_end_date);
        $year = $end_date->year;
        $month = $end_date->month;

        $total_paid = SubcontractorPayment::where(function ($query) use ($year, $month) {
            // Year is less than end year
            $query->where('year', '<', $year)
                // OR (year is equal to end year AND month <= end month)
                ->orWhere(function ($q) use ($year, $month) {
                    $q->where('year', $year)
                        ->where('month', '<=', $month);
                });
        })
            ->where('subcon_auto_id', $subcon_auto_id)
            ->sum('grand_total');

        $total_invoice_amount = SubcontractorService::where('month', '<=', $month)
            ->where('year', '<=', $year)
            ->where('subcon_auto_id', $subcon_auto_id)
            ->sum('grand_total');
        $opening_balance = SubcontractorInfo::where('subcon_auto_id', $subcon_auto_id)->first()->opening_balance;


        // dd($total_invoice_amount, $total_paid,($total_invoice_amount-$total_paid),$previous_month_end_date,$subcon_auto_id,$month);
        return   $opening_balance + $total_invoice_amount - $total_paid;
    }

    public function processASubcontractorUptoTodayBalanceSummary($subcon_auto_id)
    {

        $total_paid = SubcontractorPayment::where('act_status', 10)
            ->where('subcon_auto_id', $subcon_auto_id)
            ->sum('grand_total');

        $total_invoice_amount = SubcontractorService::where('subcon_auto_id', $subcon_auto_id)
            ->sum('grand_total');
        $opening_balance = SubcontractorInfo::where('subcon_auto_id', $subcon_auto_id)->first()->opening_balance;

        return ['total_invoice' => $total_invoice_amount + $opening_balance, 'total_payment' => $total_paid];
    }

    public function processASubcontractorSingleMonthTotalInvoiceAndPaidAmount($subcon_auto_id,$month,$year)
    {

        //  dd($previous_month_end_date,$subcon_auto_id);
        $total_paid = SubcontractorPayment::where('act_status', 10)
            ->where('subcon_auto_id', $subcon_auto_id)
            ->where('month', $month)
            ->where('year', $year)
            ->sum('grand_total');

        $total_invoice_amount = SubcontractorService::where('subcon_auto_id', $subcon_auto_id)
            ->where('month', $month)
            ->where('year', $year)
            ->sum('grand_total');
        return ['total_invoice' => $total_invoice_amount, 'total_payment' => $total_paid];
    }


    public function processASubcontractorSinlgeMonthManpowerSummaryGroupByProject($subcon_auto_id, $month, $year)
    {
        $sp = $this->getASubcontractorById($subcon_auto_id);
        return SalaryHistory::select(
            'project_infos.proj_name',
            'salary_histories.project_id',
            DB::raw("COUNT(slh_auto_id) as total_emp"),
            DB::raw("sum(slh_total_hours) as total_hours"),
            DB::raw("SUM(slh_total_salary) as total_salary"),
        )
            ->leftjoin('employee_infos', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
            ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
            ->where('slh_month', $month)->where('slh_year', $year)
            ->where('employee_infos.sponsor_id', $sp->sponsor_id)
            ->groupBy("salary_histories.project_id", 'project_infos.proj_name')
            ->get();
    }

    // SIngle Month Summary Report that will be same with service process method
    public function processASubcontractorSinlgeMonthManpowerSummaryForReport($subcon_sponsor_id, $month, $year)
    {

        return SalaryHistory::select(
            'salary_histories.slh_month',
            'months.month_name',
            'salary_histories.slh_year',
            DB::raw("COUNT(slh_auto_id) as total_emp"),
            DB::raw("sum(slh_total_hours) as total_hours"),
            DB::raw("SUM(slh_total_salary) as total_salary"),
        )
            ->leftjoin('employee_infos', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
            ->leftjoin('months', 'salary_histories.slh_month', '=', 'months.month_id')
            ->where('slh_month', $month)->where('slh_year', $year)
            ->where('employee_infos.sponsor_id', $subcon_sponsor_id)
            ->groupBy("slh_month", 'slh_year', 'month_name')
            ->first();
    }

    public function countTotalNumberOfCurrentlyActiveEmployeesInASubcontractorForReport($subcon_sponsor_id)
    {
        return EmployeeInfo::where('employee_infos.sponsor_id', $subcon_sponsor_id)->where('employee_infos.job_status', 1)->count();
    }


    public function getASubcontractorManpowerWorkedDetailsInAMonthForReport($subcon_sponsor_id, $month, $year)
    {
      
             return EmployeeMultiProjectWorkHistory::select(
                 DB::raw("DISTINCT(emp_multi_proj_work_hist.emp_auto_id) as total_emp"),
                 DB::raw("sum(total_hour+total_overtime) as total_hours"),
                 DB::raw("SUM(total_amount) as total_salary")
            )
            ->leftjoin('employee_infos', 'emp_multi_proj_work_hist.emp_auto_id', '=', 'employee_infos.emp_auto_id')
            ->where('emp_multi_proj_work_hist.month', $month)->where('emp_multi_proj_work_hist.year', $year)
            ->where('employee_infos.sponsor_id', $subcon_sponsor_id)
            ->first();
    }


    /**
     * Fetch per-subcontractor aggregated data for the Manpower Supplier Monthly Payment report.
     * Only subcontractors that have at least one service invoice in the selected month are included.
     */
    public function getManpowerMonthlyPaymentData($subcon_auto_ids, int $month, int $year, string $prev_end_date): array
    {

        $subcontractors = SubcontractorInfo::whereIn('subcon_auto_id', $subcon_auto_ids)->where('act_status', 1)->orderBy('subcon_name')->get();
        $result = [];

        foreach ($subcontractors as $sc) {
            // Service invoice totals for the selected month/year
            $invoice = DB::table('subcontractor_services')
                ->where('subcon_auto_id', $sc->subcon_auto_id)
                ->where('month', $month)
                ->where('year', $year)
                ->selectRaw('SUM(grand_total) as total_invoice, SUM(discount) as total_discount,
                             MAX(invoice_no) as invoice_no, MAX(invoice_date) as invoice_date')
                ->first();

            // Skip subcontractors with no invoice this month
            if (!$invoice || !$invoice->total_invoice) {
                continue;
            }

            // Payment totals for the selected month/year
            $paid = DB::table('subcontractors_payment')
                ->where('subcon_auto_id', $sc->subcon_auto_id)
                ->where('month', $month)
                ->where('year', $year)
                ->where('act_status', 10)
                ->selectRaw('SUM(grand_total) as paid, MAX(payment_method) as method, MAX(remarks) as remarks')
                ->first();

            // Outstanding balance up to the end of the previous month
            $prev_balance = $this->processASubcontractorUptoPreviousMonthRemainingBalance(
                $sc->subcon_auto_id,
                $prev_end_date
            );

            $total_invoice = (float) ($invoice->total_invoice ?? 0);
            $advance_adj   = (float) ($invoice->total_discount ?? 0);
            $paid_amount   = (float) ($paid->paid ?? 0);
            $net_payable   = $prev_balance + $total_invoice - $advance_adj - $paid_amount;

            $result[] = (object) [
                'subcon_name'     => $sc->subcon_name,
                'invoice_no'      => $invoice->invoice_no      ?: '-',
                'invoice_date'    => $invoice->invoice_date     ?: '-',
                'total_invoice'   => $total_invoice,
                'paid_this_month' => $paid_amount,
                'prev_balance'    => $prev_balance,
                'advance_adj'     => $advance_adj,
                'net_payable'     => $net_payable,
                'payment_method'  => $paid->method  ?? null,
                'remarks'         => $paid->remarks  ?: '-',
            ];
        }

        return $result;
    }

    public function subContractorTotalInvoiceAmount($subcontract_id)
    {
        return DB::table('subcontractor_services')->where('subcon_auto_id', $subcontract_id)->sum('grand_total');
    }


    public function subContractorTotalPaidAmount($subcontract_id)
    {
        return DB::table('subcontractors_payment')
            ->where('subcon_auto_id', $subcontract_id)->sum('grand_total');
    }

    public function subContractorTotalPaymentTransaction($subcontract_id)
    {
        return DB::table('subcontractor_services AS s')
            ->leftJoin('subcontractors_payment AS p', function ($join) use ($subcontract_id) {
                $join->on('s.subcon_auto_id', '=', 'p.subcon_auto_id')
                    ->on('s.month', '=', 'p.month')
                    ->on('s.year', '=', 'p.year');
            })
            ->select(
                's.month',
                's.year',
                DB::raw('SUM(s.grand_total) AS invoice_amount'),
                DB::raw('COALESCE(SUM(p.grand_total), 0) AS pay_amount'),
                DB::raw('CASE
                        WHEN COALESCE(SUM(p.grand_total), 0) = 0 THEN "unpaid"
                        WHEN COALESCE(SUM(p.grand_total), 0) < SUM(s.grand_total) THEN "Partial"
                        WHEN COALESCE(SUM(p.grand_total), 0) > SUM(s.grand_total) THEN "Advance"
                        ELSE "Paid"
                    END AS payment_type'),
                DB::raw('MAX(p.payment_method) AS payment_method'),
                DB::raw('MAX(p.payment_file) AS payment_file'),
                DB::raw('MAX(p.remarks) AS payment_remarks')
            )
            ->where('s.subcon_auto_id', $subcontract_id)
            ->groupBy('s.month', 's.year')
            ->orderBy('s.year', 'DESC')
            // ->orderByRaw('STR_TO_DATE(CONCAT("01 ", s.month, " ", s.year), "%d %M %Y")')
            ->get();
    }



    /*
    =========================================================================================
    ============================= SUBCONTRACTOR REPORT SECTION===============================
    =========================================================================================
    */

    function processSubcontractorsSinlgeMonthWorkSummaryReport($subcon_auto_ids, $month_id, $year_id)
    {

        // Step 1: Subquery to pre-aggregate multi-project work hours per employee
        $hoursSubquery = DB::table('emp_multi_proj_work_hist')
            ->select('emp_id')
            ->selectRaw('SUM(COALESCE(total_hour, 0)) as total_basic_hours')
            ->selectRaw('SUM(COALESCE(total_overtime, 0)) as total_ot_hours')
            ->where('month', $month_id)
            ->where('year', $year_id)
            ->groupBy('emp_id');

        // Step 2: Main query linking everything without Fan-Out data inflation
        return DB::table('subcontractor_infos as sub')
            ->leftJoin('employee_infos as emp', 'sub.sponsor_id', '=', 'emp.sponsor_id')
            // Link our unique hours aggregation
            ->leftJoinSub($hoursSubquery, 'work_agg', function ($join) {
                $join->on('emp.emp_auto_id', '=', 'work_agg.emp_id');
            })
            // Link the single-record monthly salary history entries
            ->leftJoin('salary_histories as sal', function ($join) use ($month_id, $year_id) {
                $join->on('emp.emp_auto_id', '=', 'sal.emp_auto_id')
                    ->where('sal.slh_month', '=', $month_id)
                    ->where('sal.slh_year', '=', $year_id);
            })
            ->select(
                'sub.subcon_auto_id',
                'sub.subcon_name',
                /* Accurately counts physical worker headcount */
                DB::raw('COUNT(DISTINCT sal.emp_auto_id) as total_emp'),
                DB::raw('SUM(COALESCE(work_agg.total_basic_hours, 0)) as total_work_hours'),
                DB::raw('SUM(COALESCE(work_agg.total_ot_hours, 0)) as total_ot_hours'),
                /* Standard SUM is perfectly safe now since work_agg has exactly 1 row per employee */
                DB::raw('SUM(COALESCE(sal.slh_total_salary, 0)) as total_salary_amount')
            )
            ->groupBy('sub.subcon_auto_id', 'sub.subcon_name')
            ->get();
    }
}
