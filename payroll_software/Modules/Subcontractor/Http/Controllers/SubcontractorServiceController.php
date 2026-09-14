<?php

namespace Modules\Subcontractor\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\DataServices\EmployeeRelatedDataService;
use App\Http\Controllers\DataServices\SalaryProcessDataService;
use App\Http\Controllers\DataServices\InventoryItemSetupDataService;
use Modules\Subcontractor\Services\SubcontractorDataService;
use Modules\Subcontractor\Entities\SubcontractorService;
use Carbon\Carbon;
class SubcontractorServiceController extends Controller
{

    protected $subcontractorService;

    public function __construct(SubcontractorDataService $subcontractorService)
    {
        $this->subcontractorService = $subcontractorService;
    }

    // Show the main service list page
    public function index()
    {
       // $services = SubcontractorService::all();
        $subcontractors = (new SubcontractorDataService())->getAllActiveSubcontractorsForDropdownList();
        $item_categories = (new EmployeeRelatedDataService())->getEmpAllCategoryInfoForDropdown();
       // dd($subcontractors);
        return view('subcontractor::pages.services.index', ['service_form_data'=>
        [
            'subcontractors' =>$subcontractors,
            'item_categories' => $item_categories,
        ]
        ]);
    }

    // Show the form to create a new service
    public function create()
    {
        return view('services.create');
    }

    // Store a new service
    public function store(Request $request)
    {
        try{


                 $request->validate([
                    'subcon_auto_id' => 'required|numeric',
                    'no_of_unit'     => 'required|numeric',
                    'per_unit_rate'  => 'required|numeric',
                    'discount'       => 'nullable|numeric',
                    'month'          => 'required|integer|min:1|max:12',
                    'year'           => 'required|integer',
                    'invoice_date'   => 'required|date',
                    'invoice_no'     => 'required|string',
                    'service_type'   => 'required|in:1,2,4,6,10',
                    'remarks'        => 'nullable|string',
                ]);
                 $data = $request->all();

                $file_path = null;
                if ($request->hasFile('service_invoice') && is_file($request->file('service_invoice')))
                {
                  
                    $file_path =  (new UploadDownloadController())->uploadSubcontractorInvoiceFile($request->file('service_invoice'),null);
                }
                $data['service_invoice'] = $file_path;
                $login_id = Auth::user()->id;
                $data['created_by'] = $login_id;
                $data['approved_by'] = $login_id;
                $lastDate = Carbon::create($data['year'], $data['month'],1)->lastOfMonth();
                $data['invoice_date'] = $lastDate->format('Y-m-d'); // 2024-06-30


                $arecord = $this->subcontractorService->storeSubcontractorService($data);

                return response()->json(['message' => 'Subcontractor saved successfully!', 'data' => $arecord], 201);
        }catch(Exception $ex){
            return response()->json(['message' =>'Operation Failed with Invalid Data, Please Try Again '],404);
        }


    }

    public function processSubcontractorWorkSalary(Request $request)
    {
        try{


                // 1. Create the validator manually using the Validator Facade
                $validator = Validator::make($request->all(), [
                    'subcon_auto_id' => 'required|numeric',
                    'month'          => 'required|integer|min:1|max:12',
                    'year'           => 'required|integer',
                ]);

                // 2. Check if it failed, and return your custom JSON response
                if ($validator->fails()) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Required field Validation Failed!',
                        'errors'  => $validator->errors()
                    ], 422);
                }

                $lastDate = Carbon::create(  $request->year,$request->month, 1)->lastOfMonth();
                $invoice_date = $lastDate->format('Y-m-d');

                $asubcon = $this->subcontractorService->getASubcontractorById($request->subcon_auto_id);
                if(is_null($asubcon)){
                    return response()->json(['message' => 'Subcontractor not Found!'], 404);
                }
                // $asponsor = (new EmployeeRelatedDataService())->findASponserBySponsorNameAsSubContractor($asubcon->subcon_name);
                // if(is_null($asponsor)){
                //     return response()->json(['message' => 'Subcontractor not Found!'], 404);
                // }
              // $salary_record = (new SalaryProcessDataService())->getASponsorYearlySalarySummaryMonthByMonthReport($asubcon->sponsor_id,$request->month,$request->year);
                $salary_record = $this->subcontractorService->processASubcontractorSinlgeMonthManpowerSummaryForReport($asubcon->sponsor_id,$request->month,$request->year);
                // this will be same with subcontract report process

                $total_salary_amount = $salary_record == null ? 0: $salary_record->total_salary;
                $login_id = Auth::user()->id;
                $existing_record = $this->subcontractorService->findASubcontractorServiceRecord($request['subcon_auto_id'],$request['month'],$request['year'],$request['service_type']);

                if($existing_record){

                        $existing_record->invoice_date = $invoice_date;
                        $existing_record->updated_by = $login_id;
                        $existing_record->approved_by = $login_id;
                        $existing_record->no_of_unit = 1;
                        $existing_record->per_unit_rate = $total_salary_amount;
                        $existing_record->total_amount = $total_salary_amount;
                        $existing_record->grand_total = Round($total_salary_amount);
                        $existing_record->discount = 0;
                        $newrecord = $this->subcontractorService->updateSubcontractorService($existing_record->subcon_service_auto_id,$existing_record);
                    }else{
                        $data = $request->all();
                        $request['invoice_date'] = $invoice_date;
                        $data['created_by'] = $login_id;
                        $data['approved_by'] = $login_id;
                        $data['no_of_unit'] = 1;
                        $data['per_unit_rate'] =  $total_salary_amount;
                        $data['total_amount'] =  $total_salary_amount;
                        $data['discount'] = 0;
                        $data['grand_total'] = Round($total_salary_amount);
                        $newrecord = $this->subcontractorService->storeSubcontractorService($data);
                    }
                return response()->json(['message' => 'Processing Completed Successfully!','data'=>$existing_record],201);
                // status

        }catch(Exception $ex){
            return response()->json(['message' =>'Operation Failed with Invalid Data, Please Try Again '],404);
        }


    }



    // Show the edit form
    public function edit($id)
    {
        $service = SubcontractorService::findOrFail($id);
        return view('services.edit', compact('service'));
    }

    // Update service
    public function update(Request $request, $id)
    {
         try{

                 $request->validate([
                    'subcon_auto_id' => 'required|numeric',
                    'no_of_unit'     => 'required|numeric',
                    'per_unit_rate'  => 'required|numeric',
                    'discount'       => 'nullable|numeric',
                    'month'          => 'required|integer|min:1|max:12',
                    'year'           => 'required|integer',
                    'invoice_date'   => 'required|date',
                    'invoice_no'     => 'required|string',
                    'service_type'   => 'required|in:1,2,4,6,10',
                    'remarks'        => 'nullable|string',
                ]);

                $service = SubcontractorService::findOrFail($id);

                $file_path = null;
                if ($request->hasFile('service_invoice') && is_file($request->file('service_invoice')))
                {                                 
                     $file_path =  (new UploadDownloadController())->uploadSubcontractorInvoiceFile($request->file('service_invoice'),null);

                }
                return response()->json(['message' => ' successfully!', 'data' => $request->all()], 404);

                $service->update([
                    'invoice_date'          => $request->invoice_date,
                    'invoice_no'          => $request->invoice_no,
                    'no_of_unit'     => $request->no_of_unit,
                    'per_unit_rate'  => $request->per_unit_rate,
                    'total_amount'   => $request->total_amount,
                    'discount'       => $request->discount ?? 0,
                    'grand_total'   => $request->grand_total,
                    'month'          => $request->month,
                    'year'           => $request->year,
                    'service_type'   => $request->service_type,
                    'remarks'        => $request->remarks,
                    'updated_by'     => Auth::user()->id,
                    'service_invoice' => $file_path != null ? $file_path :  $service->service_invoice
                ]);

               // return redirect()->route('services.index')->with('success', 'Service updated successfully!');

                return response()->json(['message' => 'Subcontractor saved successfully!', 'data' => $service], 201);
        }catch(Exception $ex){
            return response()->json(['message' =>'Operation Failed with Invalid Data, Please Try Again '],404);
        }
    }

    // Delete service
    public function destroy($id)
    {
        $service = SubcontractorService::findOrFail($id);
        $service->delete();
        return redirect()->route('services.index')->with('success', 'Service deleted successfully!');
    }


    public function searchServicesForListView(Request $request)
    {

         // 1. Create the validator manually using the Validator Facade
                $validator = Validator::make($request->all(), [
                    'subcon_auto_id' => 'required|numeric',
                    'month'          => 'required|integer|min:1|max:12',
                    'year'           => 'required|integer',
                ]);

                // 2. Check if it failed, and return your custom JSON response
                if ($validator->fails()) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Required field Validation Failed!',
                        'errors'  => $validator->errors()
                    ], 422);
                }

            $services =  $this->subcontractorService->searchServicesForListView($request->subcon_auto_id,$request->month,$request->year);

            return response()->json(['message' => 'Subcontractor saved successfully!', 'data' => $services,], 201);



    }



    public function showServiceAPI($id)
    {
        try {
            $service = SubcontractorService::with([
                'subcontractor:subcon_auto_id,subcon_name',
                'approvedUser:id,name',
                'createdUser:id,name',
                'updatedUser:id,name'
            ])
                ->where('subcon_service_auto_id', $id)
                ->firstOrFail();

            // Transform the response data if needed
            $responseData = [
                'service' => $service,
                'service_type_text' => $this->getServiceTypeText($service->service_type)
            ];

            return response()->json([
                'success' => true,
                'data' => $responseData
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Subcontractor service not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch subcontractor service details',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    // Convert service_type code to text
    private function getServiceTypeText($type)
    {
        // Log::info("Getting service type text for type: {$type}", [
        //     'method' => __METHOD__,
        //     'type_received' => $type,
        //     'request_data' => request()->all()
        // ]);

        // Convert to integer if it's a numeric string
        $type = is_numeric($type) ? (int)$type : $type;

        return match ($type) {
            1 => 'Manpower',
            5 => 'Other',
            10 => 'Reserved',
            default => 'Unknown'
        };
    }


    public function updateServiceAPI(Request $request, $id)
    {
        

        try {
            // Find the record using custom primary key           
             $request->validate([
                    'subcon_auto_id' => 'required|numeric',
                    'no_of_unit'     => 'required|numeric',
                    'per_unit_rate'  => 'required|numeric',
                    'discount'       => 'nullable|numeric',
                    'month'          => 'required|integer|min:1|max:12',
                    'year'           => 'required|integer',
                    'invoice_date'   => 'required|date',
                    'invoice_no'     => 'required|string',
                    'service_type'   => 'required|in:1,2,4,6,10',
                    'remarks'        => 'nullable|string',
                ]);

                $service = SubcontractorService::findOrFail($id);

                $file_path = null;
                if ($request->hasFile('service_invoice') && is_file($request->file('service_invoice')))
                {
                    $file_path =  (new UploadDownloadController())->uploadSubcontractorInvoiceFile($request->file('service_invoice'),null);                   

                }

                $service->update([
                    'invoice_date'          => $request->invoice_date,
                    'invoice_no'          => $request->invoice_no,
                    'no_of_unit'     => $request->no_of_unit,
                    'per_unit_rate'  => $request->per_unit_rate,
                    'total_amount'   => $request->total_amount,
                    'discount'       => $request->discount ?? 0,
                    'grand_total'   => $request->grand_total,
                    'month'          => $request->month,
                    'year'           => $request->year,
                    'service_type'   => $request->service_type,
                    'remarks'        => $request->remarks,
                    'updated_by'     => Auth::user()->id,
                    'service_invoice' => $file_path != null ? $file_path :  $service->service_invoice
                ]);


            return response()->json([
                'success' => true,
                'message' => 'Service updated successfully',
                'data' => $service->fresh() // Returns refreshed model data
            ],200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Service record not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update service information',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    // In your SubcontractorServiceController.php
    public function destroyService($id)
    {
        try {

            $service = SubcontractorService::where('subcon_service_auto_id', $id)->firstOrFail();

            if ($service->service_invoice) {
                 (new UploadDownloadController())->deleteSubcontractorInvoiceFile($service->service_invoice );
            }
            $service->delete();

            return response()->json([
                'success' => true,
                'message' => 'Service deleted successfully'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error deleting service: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete service',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
