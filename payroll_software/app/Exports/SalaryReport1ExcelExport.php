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

class SalaryReport1ExcelExport implements FromCollection, WithHeadings, WithMapping,ShouldAutoSize, WithStyles 
{
    protected  $salary_report_records,$month_year_records;
    /**
    * @return \Illuminate\Support\Collection
    */
    function __construct($salary_report_records,$month_year_records){
        $this->salary_report_records = $salary_report_records;
        $this->month_year_records = $month_year_records;
    }

    public function collection()
    {
         return $this->salary_report_records;
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
        $excell_header = ['Project Name']; 
        $counter = 1;
         foreach( $this->month_year_records as $my){            
            $excell_header[$counter++] = $my['month']."-".$my['year'];
        }
        $excell_header[$counter] = "Total Amount"; 
        return $excell_header;
    }
  
    public function map($item): array
    {        
       $arecord =  array_fill(0, count($this->month_year_records) + 2, 0);
       $arecord[0] = $item->proj_name; // First Column Value
       $ground_total_amount = 0;
       $counter = 1;
       foreach($item->salary_record as $amount){            
          $arecord[$counter++] = $amount > 0 ? $amount : "0";
          $ground_total_amount += $amount;
       }
       $arecord[$counter] = $ground_total_amount;  // Last Column Value
       return $arecord;
        
    }
}
