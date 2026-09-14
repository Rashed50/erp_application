<?php

namespace App\Http\Controllers\Exports;

use App\Http\Controllers\DataServices\EmployeeDataService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\EmployeeInfo;

class EmployeeHRInfoExport implements FromCollection, WithHeadings
{

   
    protected $projectIdList, $sponserId, $jobStatus, $empCategory, $empTypeId;

    function __construct($projectIdList, $sponserId, $empCategory, $jobStatus, $empTypeId)
    {
        $this->projectIdList = $projectIdList;
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
            'Nationality', 'Hourly Employee', 'Trade Name', 'Employee Type', 'Employee Status'  
        ];
    }


    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $empllist = (new EmployeeDataService())->exportEmployeeHRSectionInformation($this->projectIdList,
        $this->sponserId,
        $this->jobStatus,
        $this->empCategory,
        $this->empTypeId);
        return collect($empllist);
    }
}
