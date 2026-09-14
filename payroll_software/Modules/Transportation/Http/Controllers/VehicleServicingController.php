<?php

namespace Modules\Transportation\Http\Controllers;

use App\Http\Controllers\DataServices\ProjectDataService;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Transportation\Entities\{VehicleServicing, VehicleServicingDetails, VehicleServicingName};
use Modules\Transportation\Services\VehicleServicingService;
use App\Http\Controllers\DataServices\TransportationDataService;
use App\Http\Controllers\DataServices\EmployeeDataService;

use Illuminate\Support\Facades\{Log, Auth, Validator,DB};

use App\Models\Vehicle;
use App\Models\User;

class VehicleServicingController extends Controller
{

     protected $vehicleServicingService;

    public function __construct(VehicleServicingService $vehicleServicingService)
    {
        $this->vehicleServicingService = $vehicleServicingService;
    }

    public function index()
    {

        $drivers = (new TransportationDataService())->getAllActiveDriverInfoForDropdown();
        $vehicles = (new TransportationDataService())->getAllVehiclesInfoForDropdown();
        $servicing_names = $this->vehicleServicingService ->getListOfVehicleServicingNameForDropdownList();
        $projects = (new ProjectDataService())->getAllActiveProjectListForDropdown(1);


        return view('transportation::index',[
            'data_for_servicing_form' => [
                'drivers' => $drivers,
                'vehicles'      => $vehicles,
                'servicing_names'=>$servicing_names,
                'projects'=>$projects,
            ]
        ]);
    }


    public function store(Request $request)
    {
        try{
            //ALTER TABLE `vehicle_servicing` ADD `invoice_no` VARCHAR(256) NULL AFTER `service_by`;

            $data = $request->all();
            $validator = $this->vehicleServicingService->validateVehicleServicingData($data);
            $data['created_by'] = Auth::user()->id;
            $data['approved_by'] = Auth::user()->id;
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors(),'data'=>$request->all()], 422);
            }
            $file_path = null;
            if ($request->hasFile('servicing_invoice_file') && is_file($request->file('servicing_invoice_file'))) {
                $file_path = $this->vehicleServicingService->handleSingleFileUpload('veh_serv', $request->file('servicing_invoice_file'));
            }
            $data['invoice_file'] = $file_path;

            $service = $this->vehicleServicingService->createVehicleServicing($data);
            return response()->json(['message' => 'Successfully Saved', 'data' => $data], 201);

        }catch(Exception $ex){
            return response()->json(['errors' => $ex,'data'=>$request->all()], 500);
        }
    }


    public function detailsAPI($veh_id)
    {

        try {
            // Get the vehicle with relationships
            $vehicle = Vehicle::with([
                'employee',
                'user',
                'servicings' => function($query) {
                    $query->orderBy('start_date', 'desc');
                },
                'servicings.serviceBy'
            ])->find($veh_id);

            if (!$vehicle) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vehicle not found'
                ], 404);
            }

            // Format the response
            $response = [
                'success' => true,
                'data' => [
                    'vehicle' => [
                        'id'   => $vehicle->veh_id,
                        'name' => $vehicle->veh_name,
                        'plate_number' => $vehicle->veh_plate_number,
                        'model_number' => $vehicle->veh_model_number,
                        'brand_name'   => $vehicle->veh_brand_name,
                        'color' => $vehicle->veh_color,
                        'price' => $vehicle->veh_price,
                        'purchase_date' => $vehicle->veh_purchase_date,
                        'current_meter' => $vehicle->veh_present_metar,
                        'insurance' => [
                            'certificate' => $vehicle->veh_ins_certificate,
                            'expire_date' => $vehicle->veh_ins_expire_date,
                            'renew_date'  => $vehicle->veh_ins_renew_date,
                        ],
                        'registration' => [
                            'certificate' => $vehicle->veh_reg_certificate,
                            'expire_date' => $vehicle->veh_reg_expire_date,
                            'renew_date'  => $vehicle->veh_reg_renew_date,
                        ],
                        'photo'   => $vehicle->veh_photo,
                        'remarks' => $vehicle->remarks,
                        'status'  => $vehicle->status,
                        'driver'  => $vehicle->employee ? [
                            'id'   => $vehicle->employee->emp_auto_id,
                            'name' => $vehicle->employee->emp_name,
                        ] : null,
                        'created_by' => $vehicle->user ? $vehicle->user->name : null,
                    ],
                    'servicing_history' => $vehicle->servicings->map(function($servicing) {
                        return [
                            'id' => $servicing->veh_ser_auto_id,
                            'grand_total' => $servicing->grand_total_amount,
                            'discount'    => $servicing->discount,
                            'payable_amount' => $servicing->payable_amount,
                            'payment_method' => $servicing->payment_method,
                            'status'      => $servicing->servicing_status,
                            'start_date'  => $servicing->start_date,
                            'end_date'    => $servicing->end_date,
                            'invoice'     => $servicing->invoice_file,
                            'remarks'     => $servicing->remarks,
                            'service_by'  => $servicing->serviceBy ? $servicing->serviceBy->name : null,
                            'approved_by' => $servicing->approved_by,
                        ];
                    }),
                ]
            ];

            return response()->json($response);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // return vehicle maintenance data edit request
    public function getServicingDetails($servicingId)
    {
        $validator = Validator::make(['servicing_id' => $servicingId], [
            'servicing_id' => 'required|integer|exists:vehicle_servicing,veh_ser_auto_id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $servicing = VehicleServicing::with([
                'vehicle',
                'serviceBy',
                'details' => function($query) {
                    $query->with('serviceName');
                },
                'approvedBy',
                'createdBy',
                'updatedBy'
            ])->find($servicingId);
            // Log::info(json_encode($servicing));

            // $record = $this->vehicleServicingService->getVehicleMaintenanceRecordById($servicingId);
            // if (!$record) {
            //     return response()->json(['error' => 'Record not found'], 404);
            // }
            // Log::info(json_encode($record));


            if (!$servicing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Servicing record not found'
                ], 404);
            }

            // Format the response
            $response = [
                'success' => true,
                'data' => [
                    'servicing' => [
                        'id' => $servicing->veh_ser_auto_id,

                        'vehicle' => $servicing->vehicle ? [
                            'id'    => $servicing->vehicle->veh_id,
                            'name'  => $servicing->vehicle->veh_name,
                            'plate_number' => $servicing->vehicle->veh_plate_number
                        ] : null,

                        'grand_total_amount' => $servicing->grand_total_amount,
                        'discount'           => $servicing->discount,
                        'payable_amount'     => $servicing->payable_amount,
                        'payment_method'     => $servicing->payment_method == 1 ? 'Cash' : 'Bank',
                        'status'             => (bool)$servicing->servicing_status,
                        'start_date'         => $servicing->start_date,
                        'end_date'           => $servicing->end_date,

                        'service_by' => $servicing->serviceBy ? [
                            'id'    => $servicing->serviceBy->employee_id,
                            'name'  => $servicing->serviceBy->employee_name
                        ] : null,

                        'invoice_file' => $servicing->invoice_file,
                        'invoice_no'   => $servicing->invoice_no,
                        'remarks'      => $servicing->remarks,
                        'approved_by'  => $servicing->approvedBy ? $servicing->approvedBy->name : null,
                        'created_by'   => $servicing->createdBy ? $servicing->createdBy->name : null,
                        'created_at'   => $servicing->created_at,
                        'updated_at'   => $servicing->updated_at
                    ],

                    'service_items' => $servicing->details->map(function($detail) {
                        return [
                            'id'           => $detail->vehser_det_auto_id,
                            "service_name" => [
                                "ser_nam_auto_id" => $detail->serviceName ? $detail->serviceName->ser_nam_auto_id : null,
                                "service_name"    => $detail->serviceName ? $detail->serviceName->service_name : null
                            ],
                            'qty'     => $detail->qty,
                            'unit_rate'    => $detail->unit_rate,
                            'total_amount' => $detail->total_amount,
                            'service_type' => $detail->service_type,
                            'remarks'      => $detail->remarks
                        ];
                    })
                ]
            ];

            return response()->json($response);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function updateServicing(Request $request, $id)
    {
        // Log::info("Request ID =". $id);
        Log::info('Vehicle Servicing Update Request:', [
            'All Request Data' => $request->all()
            // 'IP' => $request->ip(),
            // 'URL' => $request->fullUrl(),
            // 'Method' => $request->method(),
            // 'User Agent' => $request->userAgent(),
            // 'User ID' => auth()->id(),
            // 'Headers' => $request->headers->all(),
        ]);

        $validator = Validator::make($request->all(), [
            'veh_auto_id'        => 'required|exists:vehicles,veh_id',
            'grand_total_amount' => 'required|numeric|min:0',
            'discount'       => 'required|numeric|min:0',
            'payable_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:1,2',
            'start_date' => 'required|date',
            'invoice_no'   => 'required|string',
            'service_by' => 'required|exists:employee_infos,employee_id',
            'remarks'    => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors'  => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // 1. Update the main servicing record
            $servicing = VehicleServicing::where('veh_ser_auto_id', $id)->firstOrFail();

            // Log::info("Service =". json_encode($servicing));

            $servicing->veh_auto_id    = $request->veh_auto_id;
            $servicing->discount       = $request->discount;
            $servicing->payable_amount = $request->payable_amount;
            $servicing->payment_method = $request->payment_method;
            $servicing->start_date     = $request->start_date;
            $servicing->end_date       = $request->end_date;
            $servicing->service_by     = $request->service_by;
            $servicing->invoice_no     = $request->invoice_no;
            $servicing->remarks        = $request->remarks;
            $servicing->updated_by     = auth()->id();
            $servicing->grand_total_amount = $request->grand_total_amount;
            $servicing->save();

            // 2. Process service items
            $existingIds = [];

            foreach ($request->servicing_list as $item) {
                $serviceData = [
                    'veh_ser_auto_id' => $servicing->veh_ser_auto_id,
                    'ser_nam_auto_id' => $item['service_name']['ser_nam_auto_id'],
                    'qty'             => $item['qty'],
                    'unit_rate'       => $item['unit_rate'],
                    'total_amount'    => $item['total_amount'],
                    'service_type'    => $item['service_type'],
                    'remarks'         => $item['remarks'] ?? null
                ];

                // Update existing or create new
                if (isset($item['id'])) {
                    VehicleServicingDetails::where('vehser_det_auto_id', $item['id'])
                        ->update($serviceData);
                    $existingIds[] = $item['id'];
                } else {
                    $newItem = VehicleServicingDetails::create($serviceData);
                    $existingIds[] = $newItem->vehser_det_auto_id;
                }
            }

            // 3. Delete items that were removed
            VehicleServicingDetails::where('veh_ser_auto_id', $servicing->veh_ser_auto_id)
                ->whereNotIn('vehser_det_auto_id', $existingIds)
                ->delete();

            DB::commit();

            // Reload the updated record with relationships
            $updatedServicing = VehicleServicing::with([
                'vehicle',
                'serviceBy',
                'details.serviceName',
                'approvedBy',
                'createdBy',
                'updatedBy'
            ])->find($servicing->veh_ser_auto_id);

            return response()->json([
                'success' => true,
                'message' => 'Vehicle servicing updated successfully',
                'data'    => $updatedServicing
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update vehicle servicing',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    public function searchVehicleSearchingRecords(Request $request){
        try{

            $records = $this->vehicleServicingService->getVehicleMaintenanceRecordsForListView($request->veh_auto_id, $request->month, $request->year);
            return response()->json(['message' => '','status' => 200,'success' => true, 'data' => $records], 200);

        }catch(Exception $ex){
            return response()->json(['message' => 'Operation failed','status' => 500,'success' => false, 'data' => $records], 500);

        }
    }


    public function deleteVehicleMaintenanceRecord($id)
    {
        try{


           $arecord =  $this->vehicleServicingService->getVehicleMaintenanceRecordById($id);
            if (!$arecord) {
                return response()->json(['error' => 'Record not found'], 404);
            }
           // $arecord->delete();
            return response()->json(['message' => 'Successfully Deleted'], 200);


        }catch(Exception $ex){
            return response()->json(['errors' => $ex,'data'=>$request->all()], 500);

        }
    }


    public function searchEmployeeByEmployeeId( $employee_id){
        try{
            $employee = (new EmployeeDataService())->getAnEmployeeInfoTableDataByEmployeeIdAndBranchOfficeId($employee_id,1);
            if($employee){
                return response()->json(['message' => '', 'success'=>true,'data' =>$employee,'errors'=>null], 200);
            }else{
                return response()->json(['errors' => 'Data Not Found','success'=>false,'message' => 'System Error'], 404);
            }
        }catch(Exception $ex){
            return response()->json(['errors' => $ex,'success'=>false,'message' => 'System Error'], 500);
        }
    }


    public function storeVehicleServiceName(Request $request){

        try{

            if(is_null($request->service_name)){
                return response()->json(['errors' => "Invalid Data"], 500);
            }
            $status_code = 201;
            if($request->ser_nam_auto_id > 0){
                $status_code = 200;
                $service = $this->vehicleServicingService->updateVehicleMaintananceServiceName($request->ser_nam_auto_id,$request->service_name,$request->ser_nam_status);
            }else{
                $service = $this->vehicleServicingService->storeVehicleMaintananceServiceName($request->service_name);
            }
            $servicing_names = $this->vehicleServicingService ->getListOfVehicleServicingNameForDropdownList();
            return response()->json(['message' => 'Successfully Completed', 'data' => $servicing_names],  $status_code);

        }catch(Exception $ex){
            return response()->json(['errors' => $ex,'data'=>$request->all()], 500);

        }

    }


    public function deleteVehicleServiceName($id){

        try{

            if(is_null($id)){
                return response()->json(['errors' => "Invalid Data"], 500);
            }
            $status_code = 200;
            if($id > 0){
                $status_code = 200;
                $service = $this->vehicleServicingService->inactiveVehicleMaintananceServiceName($id);
                $servicing_names = $this->vehicleServicingService ->getListOfVehicleServicingNameForDropdownList();
                return response()->json(['message' => 'Successfully Completed', 'data' => $servicing_names],  $status_code);
            }

        }catch(Exception $ex){
            return response()->json(['errors' => $ex], 500);

        }
    }
}
