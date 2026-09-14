<?php
namespace  Modules\Payroll\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Http\Controllers\DataServices\{EmployeeDataService,SalaryProcessDataService};

class WPSSalaryExportAsExcel implements FromCollection, WithHeadings, WithMapping,ShouldAutoSize, WithStyles
{
    protected $counter = 1;
    protected  $salary_records,$month,$year;
    /**
    * @return \Illuminate\Support\Collection
    */
    function __construct($salary_records){
        $this->salary_records = $salary_records;
       // $this->month_year_records = $month_year_records;
    }

    public function collection()
    {
        return $this->salary_records;
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
        $excell_header = ['S.N','Empl Id','Iqama No', 'Account No/IBAN','Employee Name','Bank Code','Designation','Project Name','Basic Salary','Housing Allowance','Other Allowance','Deduction','Total Amount','Remarks'];
        return $excell_header;
    }

    public function map($item): array
    {
      //  dd($item);
        $arecord =  array_fill(0,15, 0);
        $arecord[0] = $this->counter++;
        $arecord[1] =  $item->employee_id; // First Column Value
        $arecord[2] =  $item->akama_no;
        $arecord[3] =   $item->iban;// $item->account_no;
        $arecord[4] =  $item->employee_name;
        $arecord[5] =  $item->bank_code;
        $arecord[6] = '';//  $item->designation;
        $arecord[7] = ''; //  $item->proj_name;
        // $other_allowance =  ($item->slh_overtime_amount + $item->house_rent + $item->conveyance_allowance + $item->mobile_allowance  + $item->medical_allowance + $item->others1 + $item->local_travel_allowance  + $item->slh_bonus_amount);
        // // $anEmployee->gross_salary =  $anEmployee->slh_all_include_amount -  $total_deduct_amount ;
        // $basic_salary = $item->slh_all_include_amount - $other_allowance ; // food allowance is included in basic salary
        // $total_deduct_amount = $item->slh_food_deduction + $item->slh_saudi_tax + $item->slh_iqama_advance + $item->slh_other_advance + $item->slh_cpf_contribution ;

        $arecord[8] =  $item->basic_amount;
        $arecord[9] =  $item->house_rent > 0 ? $item->house_rent : '-';
        $arecord[10] = $item->other_allowance > 0 ? $item->other_allowance : '-';
        $arecord[11] = $item->deduction > 0 ? $item->deduction:'-' ;
        $arecord[12] = $item->gross_amount ;
        $arecord[13] = "";// $item->remarks;
       return $arecord;
    }
}

