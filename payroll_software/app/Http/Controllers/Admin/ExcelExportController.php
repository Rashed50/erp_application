<?php

namespace App\Http\Controllers\Admin;


//use App\Http\Controllers\Exports\EmployeeExport;
use App\Http\Controllers\Exports\EmployeeListExport;
use App\Http\Controllers\Exports\EmployeeHRInfoExport;
use App\Exports\EmpListHRReportExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Excel;

class ExcelExportController extends Controller
{

      
    
    public function exportEmployeeInformationByProjectSponserJobStatusEmpTradeEmpType($projectId, $sponserId, $empCategory, $jobStatus, $empTypeId)
    {
        
        return Excel::download(new EmployeeListExport($projectId, $sponserId, $empCategory, $jobStatus, $empTypeId), 'employee_list.xlsx');
    }

    public function exportEmployeeHRReportByProjectSponserJobStatusEmpTradeEmpType($projectIdList, $sponserId, $empCategory, $jobStatus, $empTypeId)
    {
      
        // return Excel::download(new EmployeeHRInfoExport($projectIdList, $sponserId, $empCategory, $jobStatus, $empTypeId), 'hr_employee_list.xlsx');
        return Excel::download(new EmpListHRReportExport($projectIdList, $sponserId, $empCategory, $jobStatus, $empTypeId), 'hr_employee_list.xlsx');
      
    }
}
