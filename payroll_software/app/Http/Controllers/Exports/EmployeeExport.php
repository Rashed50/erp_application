<?php

namespace App\Http\Controllers\Exports;

use App\Http\Controllers\DataServices\EmployeeDataService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\EmployeeInfo;

class EmployeeExport implements FromCollection, WithHeadings
{

    protected $connection_statusId;
    protected $packageId;

    function __construct($connection_statusId, $packageId)
    {
        $this->connectionStatusId = $connection_statusId;
        $this->packageId = $packageId;
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
        $empllist = (new EmployeeDataService())->exportEmployeeInformation();
        // dd($empllist[0]);
        return collect($empllist);
    }
}
