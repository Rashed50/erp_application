<?php

namespace App\Http\Controllers\Exports;

use App\Http\Controllers\DataServices\EmployeeDataService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\EmployeeInfo;

class EmployeeListExport implements FromCollection, WithHeadings
{


    protected $projectId, $sponserId, $jobStatus, $empCategory, $empTypeId;

    function __construct($projectId, $sponserId, $empCategory, $jobStatus, $empTypeId)
    {
        $this->projectId = $projectId;
        $this->sponserId = $sponserId;
        $this->empCategory = $empCategory;
        $this->jobStatus = $jobStatus;
        $this->empTypeId = $empTypeId;
    }

    public function headings(): array
    {
        return [
             
            'Employee ID', 'Employee Name', 'Passport No',
            'Passport Expire Date', 'Iqama No', 'Iqama Expire Date',
            'Sponsor Name', 'Project Name', 'Mobile No', 'Email', 'Date of Birth',
            'Nationality', 'Hourly Employee', 'Trade Name', 'Employee Type', 'Employee Status',
            'Basic Salary',
            'Hourly Rate',
            'Basic Hours',
            'Food Allowance',
            'Mobile Allowance'

        ];
    }


    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        
            
        $empllist = (new EmployeeDataService())->exportEmployeeInformationByProjectSponserJobStatusEmpTradeEmpType(
            $this->projectId,
            $this->sponserId,
            $this->jobStatus,
            $this->empCategory,
            $this->empTypeId
        );
        return collect($empllist);
    }
}
