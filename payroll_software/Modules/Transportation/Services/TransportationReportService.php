<?php

namespace Modules\Transportation\Services;

use Modules\Transportation\Entities\VehicleServicing;
use Modules\Transportation\Entities\VehicleServicingDetails;
use Modules\Transportation\Entities\VehicleServicingName;
use App\Models\{Vehicle,DrivVehicleRecord};
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Illuminate\Http\Request;

class TransportationReportService
{
    public function processAVehicleMaintenanceDetailsReport($vehicle_auto_id,$from_date,$to_date){

       $avehicle = Vehicle::where('status', 1)->where('veh_id', $vehicle_auto_id)->first();
       //  Vehicle::where('veh_plate_number', $plate_number)->first();
       if($avehicle == null){
            return $final_record = array(
                'vehicle' => null,
                'maintenance_records'=>null,
            );
       }
        $maintenance_records = VehicleServicingDetails::select(
                "vehicle_servicing.*",
                'vehicle_servicing_details.*',
                'vehicle_servicing_names.service_name',
                'driver_infos.*',
                'project_infos.proj_name',
                'vehicles.veh_plate_number',
                'vehicles.veh_name',
                'employee_infos.employee_name',
                'users.name as created_by'
            )
            ->join('vehicle_servicing', 'vehicle_servicing_details.veh_ser_auto_id', '=', 'vehicle_servicing.veh_ser_auto_id')
            ->join('vehicles', 'vehicle_servicing.veh_auto_id', '=', 'vehicles.veh_id')
            ->join('vehicle_servicing_names', 'vehicle_servicing_details.ser_nam_auto_id', '=', 'vehicle_servicing_names.ser_nam_auto_id')
          //  ->join('vehicles', 'vehicle_servicing.veh_auto_id', '=', 'vehicles.veh_id')
          //  ->join('vehicles', 'vehicle_servicing.veh_auto_id', '=', 'vehicles.veh_id')
            ->leftjoin('driv_vehicle_records', 'vehicles.veh_id' ,'=', 'driv_vehicle_records.veh_auto_id')
            ->leftjoin('driver_infos', 'driv_vehicle_records.driv_auto_id', '=', 'driver_infos.dri_auto_id')
            ->leftjoin('project_infos', 'driv_vehicle_records.project_id', '=', 'project_infos.proj_id')
            ->join('employee_infos', 'vehicle_servicing.service_by', '=', 'employee_infos.employee_id')
            ->join('users', 'vehicle_servicing.created_by', '=', 'users.id')
            ->where('vehicle_servicing.veh_auto_id',$vehicle_auto_id);

        // Apply date filters if provided
        if($from_date) {
            $maintenance_records = $maintenance_records->whereDate('vehicle_servicing.start_date', '>=', $from_date);
        }
        if($to_date) {
            $maintenance_records = $maintenance_records->whereDate('vehicle_servicing.start_date', '<=', $to_date);
        }

        $maintenance_records = $maintenance_records->get();
      // dd($maintenance_records);
     $DrivVehicleRecord = DrivVehicleRecord::where('driv_vehicle_records.veh_auto_id', $vehicle_auto_id)
            ->leftjoin('vehicles', 'driv_vehicle_records.veh_auto_id','=','vehicles.veh_id')
            ->leftjoin('driver_infos', 'driv_vehicle_records.driv_auto_id', '=', 'driver_infos.dri_auto_id')
            ->leftjoin('project_infos', 'driv_vehicle_records.project_id', '=', 'project_infos.proj_id')
            ->orderBy('project_infos.proj_name', 'ASC')
            ->where('vehicles.status' , 1)
            ->where('driv_vehicle_records.status',1)
            ->get();

     //   dd($DrivVehicleRecord);

        return $final_record =(Object) array(
            'vehicle' => count($DrivVehicleRecord) > 0 ? $DrivVehicleRecord[0] : null,
            'maintenance_records'=>$maintenance_records,
        );
    }

    public function processProjectMaintenanceDetailsReport($project_ids, $from_date, $to_date){

        // Convert comma-separated string to array if needed
        $project_array = is_string($project_ids) ? explode(',', $project_ids) : (array) $project_ids;
        $project_array = array_values(array_filter(array_map(static function ($projectId) {
            return is_string($projectId) ? trim($projectId) : $projectId;
        }, $project_array), static function ($projectId) {
            return $projectId !== null && $projectId !== '';
        }));

        $maintenance_records = VehicleServicingDetails::select(
                "vehicle_servicing.*",
                'vehicle_servicing_details.*',
                'vehicle_servicing_names.service_name',
                'driver_infos.*',
                'project_infos.proj_name',
                'vehicles.veh_plate_number',
                'vehicles.veh_name',
                'vehicles.veh_id',
                'employee_infos.employee_name',
                'users.name as created_by'
            )
            ->join('vehicle_servicing', 'vehicle_servicing_details.veh_ser_auto_id', '=', 'vehicle_servicing.veh_ser_auto_id')
            ->join('vehicles', 'vehicle_servicing.veh_auto_id', '=', 'vehicles.veh_id')
            ->join('vehicle_servicing_names', 'vehicle_servicing_details.ser_nam_auto_id', '=', 'vehicle_servicing_names.ser_nam_auto_id')
            ->leftjoin('driv_vehicle_records', 'vehicles.veh_id' ,'=', 'driv_vehicle_records.veh_auto_id')
            ->leftjoin('driver_infos', 'driv_vehicle_records.driv_auto_id', '=', 'driver_infos.dri_auto_id')
            ->leftjoin('project_infos', 'driv_vehicle_records.project_id', '=', 'project_infos.proj_id')
            ->join('employee_infos', 'vehicle_servicing.service_by', '=', 'employee_infos.employee_id')
            ->join('users', 'vehicle_servicing.created_by', '=', 'users.id')
            ->whereIn('driv_vehicle_records.project_id', $project_array)
            ->orderBy('vehicles.veh_name', 'ASC')
            ->orderBy('vehicle_servicing.start_date', 'DESC');

        // Apply date filters if provided
        if($from_date) {
            $maintenance_records = $maintenance_records->whereDate('vehicle_servicing.start_date', '>=', $from_date);
        }
        if($to_date) {
            $maintenance_records = $maintenance_records->whereDate('vehicle_servicing.start_date', '<=', $to_date);
        }

        $records = $maintenance_records->get();

        return $records;
    }

}
