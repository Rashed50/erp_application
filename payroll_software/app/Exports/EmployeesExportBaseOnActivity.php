<?php

namespace App\Exports;


use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;

use App\Http\Controllers\DataServices\EmployeeDataService;
use App\Http\Controllers\DataServices\EmployeeAttendanceDataService;
use App\Http\Controllers\DataServices\EmployeeRelatedDataService;
use App\Models\EmployeeInfo;


class EmployeesExportBaseOnActivity implements FromCollection, WithHeadings, WithMapping,ShouldAutoSize, WithStyles
{

    protected $records;

    function __construct($records)
    {
        $this->records = $records;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
         return collect($this->records);
    }

    public function headings(): array
    {
        return [
            'Employee ID', 'Employee Name', 'Iqama No',
            'Expire Date', 'Passport No', 'Expire Date',
            'Sponsor Name', 'Mobile No', 'Email', 'Salary Type',
            'Nationality', 'Runaway Date', 'Inserted By', 'Activity', 'Current Status'
        ];
    }
     public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
             1    => ['font' => ['bold' => true]],
        ];
    }

    public function map($emp): array
    {
        return [
            $emp->employee_id,
            $emp->employee_name,
            $emp->akama_no,
            '',
            $emp->passfort_no,
            '',
            $emp->spons_name,
            '',
            '',
            $emp->hourly_employee == 1 ? 'Hourly' : 'Basic Salary',
            $emp->country_name,
            $emp->create_at,
            $emp->name,
            $emp->title,
            $this->getEmployeeJobStatus($emp->job_status),


        ];
    }




    private function getEmployeeJobStatus($job_status)
    {
        if($job_status == 1)
            return   "Active";
        elseif($job_status == 2)
            return  "Inactive";
        elseif($job_status == 3)
            return  "Final_Exit";
        elseif($job_status == 4)
            return  "Relased";
        elseif($job_status ==5)
            return  "Vacation";
        elseif($job_status == 6)
            return  "Run_Away";
        else
            return  "Not_Assigned";

    }
}
