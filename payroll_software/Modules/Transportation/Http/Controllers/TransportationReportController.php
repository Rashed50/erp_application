<?php

namespace Modules\Transportation\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Modules\Transportation\Services\{VehicleServicingService,TransportationReportService};
use App\Http\Controllers\DataServices\{TransportationDataService,ProjectDataService,CompanyDataService};
// use App\Http\Controllers\DataServices\EmployeeDataService;

use Illuminate\Support\Facades\{Log, Auth, Validator,DB};
use App\Models\ProjectInfo;

// use App\Models\Vehicle;
// use App\Models\User;


class TransportationReportController extends Controller{

    protected $vehicleServicingService;

    public function __construct(VehicleServicingService $vehicleServicingService)
    {
        $this->vehicleServicingService = $vehicleServicingService;
    }

    public function reportGenerationForm(){

        $drivers = (new TransportationDataService())->getAllActiveDriverInfoForDropdown();
        $vehicles = (new TransportationDataService())->getAllVehiclesInfoForDropdown();
        $servicing_names = $this->vehicleServicingService ->getListOfVehicleServicingNameForDropdownList();
        $projects = (new ProjectDataService())->getAllActiveProjectListForDropdown(1);

        return view('transportation::reports.transport_report_generation_form',[
            'form_data' => [
                'drivers' => $drivers,
                'vehicles'      => $vehicles,
                'servicing_names'=>$servicing_names,
                'projects'=>$projects
            ]
        ]);
    }


    public function generateTransportationReport(Request $request){

        $company = (new CompanyDataService())->findCompanryProfile();
        $login_name = Auth::user()->name;

        return response()->json(['message' => 'We have to implement the report generation logic','status' => 500,'success' => false, 'data' => $request->all()], 500);

        // Check if projects are selected
        $has_projects = $request->has_projects == 1;

        if($has_projects && $request->project_ids) {
                // Show all vehicles for selected projects
                $project_ids = $request->project_ids;
                $selected_project_names = ProjectInfo::whereIn(
                        'proj_id',
                        collect(explode(',', $project_ids))
                            ->map(static function ($projectId) {
                                return trim($projectId);
                            })
                            ->filter()
                            ->values()
                            ->all()
                    )
                    ->orderBy('proj_name', 'ASC')
                    ->pluck('proj_name')
                    ->implode(', ');

                $maintenance_records = (new TransportationReportService())->processProjectMaintenanceDetailsReport(
                    $project_ids,
                    $request->from_date,
                    $request->to_date
                );

                return view('transportation::reports.avehicle_maintenance_report_projects', compact(
                    'company',
                    'maintenance_records',
                    'login_name',
                    'selected_project_names'
                ), [
                    'from_date' => $request->from_date,
                    'to_date' => $request->to_date
                ]);

        } else {

                // Show single vehicle details (original view)
                $records = (new TransportationReportService())->processAVehicleMaintenanceDetailsReport(
                    $request->vehicle_ids,
                    $request->from_date,
                    $request->to_date
                );

                return view('transportation::reports.avehicle_maintenance_report', compact(
                    'company',
                    'records',
                    'login_name'
                ));
        }
    }
}
