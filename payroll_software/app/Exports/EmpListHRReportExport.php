<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Http\Controllers\DataServices\EmployeeDataService;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EmpListHRReportExport implements FromCollection, WithHeadings,ShouldAutoSize, WithStyles
{
     protected $employees;

  //  protected $project_id_List, $trade_id_list, $sponsor_id_list,$job_status,$working_shift;

    // function __construct($project_id_List, $sponsor_id_list, $trade_id_list,$job_status,$working_shift)
    // {
    //     $this->project_id_List = $project_id_List;
    //     $this->sponsor_id_list = $sponsor_id_list;
    //     $this->trade_id_list = $trade_id_list;
    //     $this->working_shift = $working_shift;
    //     $this->job_status = $job_status;
    // }

    function __construct($employees)
    {
        $this->employees = $employees;
    }

    public function styles(Worksheet $sheet)
    {
        return [
             1   => ['font' => ['bold' => true]],
        ];
    }
    public function headings(): array
    {
        return [
            'Employee ID', 'Employee Name', 'Passport No','Iqama No', 'Expire Date','Mobile No', 'phone_no','Address','District','Division',
             'Nationality','Agency Name','Sponsor Name', 'Project Name','Trade Name','Employee Type','Hourly Employee', 'Job Status','Working Shift(0=Day, 1=Night)','Email'
        ];
    }


    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return collect($this->employees);
      //  return (new EmployeeDataService())->exportEmployeeInformationDetailsHRReport($this->project_id_List,$this->sponsor_id_list,$this->trade_id_list,$this->job_status,$this->working_shift);

    }
}
