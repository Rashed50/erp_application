<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class WPSEmployeMonthlyWorkRecordAsExcel implements FromCollection, WithHeadings, WithMapping,ShouldAutoSize, WithStyles
{
    protected $counter = 1;
    protected  $records=[];//,$month,$year;
    /**
    * @return \Illuminate\Support\Collection
    */
    function __construct($records){
        $this->records = $records;
       // $this->month_year_records = $month_year_records;
    }

    public function collection()
    {
        return $this->records;
    }
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
             1    => ['font' => ['bold' => true]],
        ];
    }

    public function headings(): array
    {
        $excell_header = ['SN','Emp ID','Employee Name','Iqama No', 'Designation','Account Number','IBAN','Bank','Email','Project Name','Total Hours',
        'Basic Hours','Over Time','Working Days','Paid Leave','Absent','Total Days','Note','Signature'];
        return $excell_header;
    }

    public function map($item): array
    {
      //  dd($item);
        $arecord =  array_fill(0,19, 0);
        $arecord[0] = $this->counter++;
        $arecord[1] =  $item->employee_id; // First Column Value
        $arecord[2] =  $item->employee_name;
        $arecord[3] =  $item->akama_no;
        $arecord[4] =  $item->catg_name;
        $arecord[5] =  $item->acc_number;
        $arecord[6] =  $item->acc_iban;
        $arecord[7] =  $item->bank_code;
        $arecord[8] =  $item->email;
        $arecord[9] =  $item->last_working_project;
        $arecord[10] =  $item->basic_hours + $item->over_time;
        $arecord[11] =  $item->basic_hours ;
        $arecord[12] =  $item->over_time ;
        $arecord[13] =  $item->duty_status == 'Full' ? 'Full Month' : $item->working_days;
        $arecord[14] =  $item->sick_leave ;
        $arecord[15] = $item->absent == 0 ? '-' : $item->absent;
        $arecord[16] = $item->present == 0 ? '-' : $item->present;
        $arecord[17] = "";
        $arecord[18] = "";

       return $arecord;
    }


}
