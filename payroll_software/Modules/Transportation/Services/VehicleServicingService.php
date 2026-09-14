<?php

namespace Modules\Transportation\Services;

use Modules\Transportation\Entities\VehicleServicing;
use Modules\Transportation\Entities\VehicleServicingDetails;
use Modules\Transportation\Entities\VehicleServicingName;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Illuminate\Http\Request;

class VehicleServicingService
{

    /**
     * Validate subcontractor data.
     */
    public function validateVehicleServicingData(array $data)
    {
        return Validator::make($data, [
            'veh_auto_id' => 'required|exists:vehicles,veh_id',
            'grand_total_amount' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'payable_amount' => 'required|numeric',
            'payment_method' => 'required|integer|in:1,2',
          //  'servicing_status' => 'boolean',
            'start_date' => 'required|date',
           // 'end_date' => 'nullable|date',
            'service_by' => 'nullable|exists:employee_infos,employee_id',
            //   'servicing_invoice_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'remarks' => 'nullable|string',

        ]);


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

    public static function handleSingleFileUpload(string $dir, $file)
    {
             if($file == null)
              return null;
            if ($file->isValid()) {
                // upload into S3 bucket Use 'put' and pass the file object directly
                return   Storage::disk('s3')->put("vehs", $file); // only the file name in the bucket

            }else
            return null;
    }

    public function createVehicleServicing($data)
    {
// ALTER TABLE `vehicle_servicing` ADD `current_mileage` INT NOT NULL AFTER `remarks`;

        $newrecord = VehicleServicing::create($data);

        $servicing_list = $data['services'] ?? [];
        foreach($servicing_list as $sn){
            $this->storeVehicleMaintananceInvoiceServicingName($newrecord->veh_ser_auto_id,$sn['id'],$sn['service_type'],$sn['qty'],$sn['unit_rate'],$sn['total_amount'],$sn['remarks']);
        }
        return $newrecord;
    }

    public function updateVehicleServicing($id, $data)
    {
        $service = VehicleServicing::findOrFail($id);

        // Handle file upload if a new file is provided
        if (isset($data['invoice_file'])) {
            // Delete old file if exists
            if ($service->invoice_file) {
                Storage::disk('public')->delete($service->invoice_file);
            }
            $filePath = $data['invoice_file']->store('invoices', 'public');
            $data['invoice_file'] = $filePath;
        }

        $service->update($data);
        return $service;
    }

    public function deleteVehicleServicing($id)
    {
        $service = VehicleServicing::findOrFail($id);

        // Delete invoice file if exists
        if ($service->invoice_file) {
            Storage::disk('public')->delete($service->invoice_file);
        }
        return $service->delete();
    }

    public function getVehicleMaintenanceRecordsForListView($veh_auto_id, $month, $year){
            $query = VehicleServicing::select(
                "vehicle_servicing.*",
                'vehicles.veh_plate_number',
                'vehicles.veh_name',
                'employee_infos.employee_name',
                'users.name as created_by'
                )
                ->join('vehicles', 'vehicle_servicing.veh_auto_id', '=', 'vehicles.veh_id')
                ->join('employee_infos', 'vehicle_servicing.service_by', '=', 'employee_infos.employee_id')
                ->join('users', 'vehicle_servicing.created_by', '=', 'users.id');
                
            if($veh_auto_id){
                $query->where('vehicle_servicing.veh_auto_id', $veh_auto_id);
            }
            if($month && $year){
                $query->whereMonth('vehicle_servicing.start_date', $month)
                      ->whereYear('vehicle_servicing.start_date', $year);
            }
            if($year && !$month){
                $query->whereYear('vehicle_servicing.start_date', $year);
            }
            return $query->orderBy('vehicle_servicing.start_date', 'DESC')->get();

    }


    public function getVehicleMaintenanceRecordById($servicingId)
    {
        // return VehicleServicing::select(
        //         "vehicle_servicing.*",
        //         'vehicles.veh_plate_number',
        //         'vehicles.veh_name',
        //         'employee_infos.employee_name',
        //         'users.name as created_by'
        //     )
        //     ->join('vehicles', 'vehicle_servicing.veh_auto_id', '=', 'vehicles.veh_id')
        //     ->join('employee_infos', 'vehicle_servicing.service_by', '=', 'employee_infos.employee_id')
        //     ->join('users', 'vehicle_servicing.created_by', '=', 'users.id')
        //     ->where('vehicle_servicing.veh_ser_auto_id', $servicingId)
        //     ->first();

        try {
            $record = VehicleServicing::select(
                    "vehicle_servicing.*",
                    'vehicles.veh_plate_number',
                    'vehicles.veh_name',
                    'employee_infos.employee_name',
                    'users.name as created_by'
                )
                ->join('vehicles', 'vehicle_servicing.veh_auto_id', '=', 'vehicles.veh_id')
                ->join('employee_infos', 'vehicle_servicing.service_by', '=', 'employee_infos.employee_id')
                ->join('users', 'vehicle_servicing.created_by', '=', 'users.id')
                ->where('vehicle_servicing.veh_ser_auto_id', $servicingId)
                ->first();

            if (!$record) {
                throw new \Exception('Record not found');
            }

            return $record;

        } catch (\Exception $e) {
            // Log the error or handle as needed
            return null;
        }
    }

    public function storeVehicleMaintananceInvoiceServicingName($veh_ser_auto_id, $ser_nam_auto_id, $service_type,$qty,$unit_rate,$total_amount, $remarks){
        VehicleServicingDetails::insert([
            'veh_ser_auto_id' =>$veh_ser_auto_id,
            'ser_nam_auto_id' =>$ser_nam_auto_id,
            'service_type' =>$service_type,
            'qty'=>$qty,
            'unit_rate'=>$unit_rate,
            'total_amount'=>$total_amount,
            'remarks' =>$remarks
        ]);
    }



    /*
        Vehicle Service Name
    */
    public function storeVehicleMaintananceServiceName($service_name){
      return  VehicleServicingName::insert([
            'service_name'=>$service_name,
        ]);

    }
    public function updateVehicleMaintananceServiceName($ser_nam_auto_id,$service_name,$ser_nam_status){
        return  VehicleServicingName::where('ser_nam_auto_id',$ser_nam_auto_id)->update([
              'service_name'=>$service_name,
              'ser_nam_status'=>$ser_nam_status,
          ]);

    }

    public function inactiveVehicleMaintananceServiceName($ser_nam_auto_id){
        return  VehicleServicingName::where('ser_nam_auto_id',$ser_nam_auto_id)->update([
              'ser_nam_status'=>0,
          ]);

    }


    public function getListOfVehicleServicingNameForDropdownList(){
        return VehicleServicingName:://select("ser_nam_auto_id,service_name")->
                orderBy("ser_nam_auto_id","DESC")
                ->get();
     }
}
