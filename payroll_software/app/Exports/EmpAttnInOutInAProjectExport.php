<?php

namespace App\Exports;

use App\Http\Controllers\DataServices\EmployeeDataService;
use App\Http\Controllers\DataServices\EmployeeAttendanceDataService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\EmployeeInfo;
use App\Enums\AttendanceTypeEnum;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
//use DateTime;

class EmpAttnInOutInAProjectExport implements FromCollection, WithHeadings, WithMapping,ShouldAutoSize, WithStyles, WithEvents
{
    protected  $working_shift,$start_day,$end_day,$numberOfDaysInThisMonth,$month,$year,$list_of_emp,$excel_row_counter = 0,$project_color_codes;

    function __construct($working_shift,$start_day,$end_day,$numberOfDaysInThisMonth,$month,$year,$list_of_emp,$project_color_codes)
    {
        $this->working_shift = $working_shift == "" ? [0,1]:[(int)$working_shift];
        $this->start_day = $start_day;
        $this->end_day = $end_day;
        $this->numberOfDaysInThisMonth = $numberOfDaysInThisMonth;
        $this->month = $month;
        $this->year = $year;
        $this->list_of_emp = $list_of_emp;
        $this->project_color_codes = $project_color_codes;
    }

    public function styles(Worksheet $sheet)
    {

        return [
            // Style the first row as bold text.
             1    => ['font' => ['bold' => true]],

            // Styling a specific cell by coordinate.
           // 'Employee Name' => ['font' => ['bold' => true]],

            // Styling an entire column.
          //  'Iqama No'  => ['font' => ['bold' => true]],
        ];
    }

    // public function columnWidths(): array
    // {
    //     return [
    //         'A' => 55,
    //         'B' => 45,
    //     ];
    // }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {

                $excel_row_counter = 1 ; // excell default header(0) + attendacnce header row(1)

                foreach($this->list_of_emp as $emp){
                       $cc = 0;
                       $excel_row_counter++; //
                       $attendence = $emp->attendance_records;
                       if(count($attendence) <= 0)
                            continue;

                        for($i =1; $i <=  $this->numberOfDaysInThisMonth;$i++){
                            if($cc < $attendence->count()){
                                $arecord = $attendence[$cc];
                            }
                            $color_code = "FFFFFF";
                            if($i == (int) $arecord->emp_io_date){
                               // $color_code = "A41007";
                                $color_code = $this->project_color_codes[$arecord->proj_id];
                               $cc++;
                            }
                            $event->sheet->getDelegate()->getStyle(($this->getExcellColumnName($i).$excel_row_counter))
                                ->getFill()
                                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                                ->getStartColor()
                                ->setARGB($color_code);
                        }
                }

            },
        ];
    }

    public function prepareRows($rows)
    {
        return $rows;
    }

    public function headings(): array
    {
        $excell_header = ['ID', 'Employee Name','Iqama No','Salary Type','Trade Name','Sponsor Name','Month,Year' ];
        $fixed_columns = 6;
        for($i = 1; $i <= $this->numberOfDaysInThisMonth; $i++)
         $excell_header[$i+ $fixed_columns] = $i;

        $fixed_columns += $fixed_columns + $this->numberOfDaysInThisMonth+1;
        $excell_header[$fixed_columns] = "Total Hours";
         $fixed_columns +=1;
        $excell_header[$fixed_columns] = "Absent";
         $fixed_columns +=1;
        $excell_header[$fixed_columns] = "Basic Hours";
         $fixed_columns +=1;
        $excell_header[$fixed_columns] = "Overtime";
         $fixed_columns +=1;
        $excell_header[$fixed_columns] = "Days";
        return $excell_header;
    }

    public function map($emp): array
    {

        $attendence = (new EmployeeAttendanceDataService())->getAnEmployeeMonthlyAttendanceDateToDateRecordsByProjectIdForExportExcell($emp->emp_auto_id,$emp->proj_id,$this->start_day,$this->end_day, $this->month, $this->year,$this->working_shift);
        $records =  array_fill(0, $this->numberOfDaysInThisMonth + 7+5, 0); // first 7 colum & last 5 colum
        $counter= 0;
        $this->list_of_emp[$this->excel_row_counter++]->attendance_records =  $attendence;

        $records[0] = $emp->employee_id;
        $records[1] = $emp->employee_name;
        $records[2] = $emp->akama_no;
        $records[3] = $emp->hourly_employee == 1 ? 'Hourly':'Basic';
        $records[4] = $emp->catg_name;
        $records[5] = $emp->spons_name;
        $records[6] = $this->getMonthName($this->month)." ".$this->year;

        if($attendence->count() == 0)
            return $records;

        $total_days = 0;
        $total_overtime =0;
        $total_work_hours = 0;
        $friday_work =0 ;

        for($i = 1; $i <= $this->numberOfDaysInThisMonth;$i++){
            if($counter < $attendence->count() )
               $arecord = $attendence[$counter];
            if($i == (int) $arecord->emp_io_date ){

                $records[$i+6] = $arecord->attendance_status == "AW" ? ("".$arecord->daily_work_hours+$arecord->over_time) : $arecord->attendance_status;
                $total_overtime += (float) $arecord->over_time;
                $total_work_hours += (float) $arecord->daily_work_hours;
                $counter +=1;
            } else {
                $records[$i+6] = "A";
            }
        }
        $records[$this->numberOfDaysInThisMonth+7] = $total_work_hours+$total_overtime;
        $records[$this->numberOfDaysInThisMonth+8] = $this->numberOfDaysInThisMonth - $attendence->count();
        $records[$this->numberOfDaysInThisMonth+9] = $total_work_hours;
        $records[$this->numberOfDaysInThisMonth+10] =   $total_overtime;
        $records[$this->numberOfDaysInThisMonth+11] = $attendence->count();

        return $records;

    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        if($this->list_of_emp->count()>0){
            return collect($this->list_of_emp);
        }
    }

    private function getMonthName($monthNumber){
        $dateObj   = \DateTime::createFromFormat('!m', $monthNumber);
        return $dateObj->format('M');
    }
    public function getExcellColumnName($index){

        switch($index){

            case 1:
                return "H";
            case 2:
                return "I";
            case 3:
                return "J";
            case 4:
                return "K";
            case 5:
                return "L";
            case 6:
                return "M";
            case 7:
                return "N";
            case 8:
                return "O";
            case 9:
                return "P";
            case 10:
                return "Q";
            case 11:
                return "R";
            case 12:
                return "S";
            case 13:
                return "T";
            case 14:
                return "U";
            case 15:
                return "V";
            case 16:
                return "W";
            case 17:
                return "X";
            case 18:
                return "Y";
            case 19:
                return "Z";
            case 20:
                return "AA";
            case 21:
                return "AB";
            case 22:
                return "AC";
            case 23:
                return "AD";
            case 24:
                return "AE";
            case 25:
                return "AF";
            case 26:
                return "AG";
            case 27:
                return "AH";
            case 28:
                return "AI";
            case 29:
                return "AJ";
            case 30:
                return "AK";
            case 31:
                return "AL";
        }
    }
}
