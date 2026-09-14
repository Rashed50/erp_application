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

class EmpAttendanceExport implements FromCollection, WithHeadings, WithMapping,ShouldAutoSize, WithStyles, WithEvents
{
    protected $projectIdList, $trade_id_list, $working_shift,$start_day,$end_day,$numberOfDaysInThisMonth,$month,$year,$list_of_emp,$excel_row_counter = 0,$project_color_codes;

    function __construct($projectIdList, $trade_id_list, $working_shift,$start_day,$end_day,$numberOfDaysInThisMonth,$month,$year,$list_of_emp,$project_color_codes)
    {
        $this->projectIdList = $projectIdList;
        $this->trade_id_list = $trade_id_list;
        $this->working_shift = $working_shift;
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

               // $event->sheet->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);


              //  dd($this->list_of_emp[1]);
                $excel_row_counter = 1 ; // excell default header(0) + attendacnce header row(1)
             //   $emp = $this->list_of_emp[1];
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
                            if($i == (int) $arecord->emp_io_date){   //   && $arecord->attendance_status != "AW"
                               // $color_code = "A41007";
                                $color_code = $this->project_color_codes[$arecord->proj_id];

                              //  $cc == 1 ? dd($arecord,$this->project_color_codes) :'';
                               $cc++;
                            }
                            // else if($i == (int) $arecord->emp_io_date ) {
                            //     $color_code = $this->project_color_codes[$arecord->proj_id];
                            // }
                            $event->sheet->getDelegate()->getStyle(($this->getExcellColumnName($i).$excel_row_counter))
                                ->getFill()
                                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                                ->getStartColor()
                                ->setARGB($color_code);
                        }

                }




            },

            // BeforeSheet::class => function (BeforeSheet $event) {
            //     // echo 200;
            //     $sheets = $event->sheet->getParent()->getSheetNames();
            //     $this->active_sheet = $sheet;
            // },

            // BeforeWriting::class => function (BeforeWriting $event) {
            //     // echo 300;
            //     $sheets = $event->writer->getSheetNames();

            // },

        ];
    }

    public function prepareRows($rows)
    {
        return $rows;
    }

    public function headings(): array
    {

        $excell_header = ['ID', 'Employee Name','Iqama No','Type','Trade Name','Month/Year' ];
        for($i = 1; $i <= $this->numberOfDaysInThisMonth; $i++)
         $excell_header[$i+5] = $i;

        // $excell_header[$i+6] = "Total Days";
        // $excell_header[$i+7] = "Total Overtime";
        // $excell_header[$i+8] = "Total Basic Hours";

        $excell_header[$i+6] = "Total Hours";
        $excell_header[$i+7] = "Basic Hours";
        $excell_header[$i+8] = "Overtime";
        $excell_header[$i+9] = "Absent";
        $excell_header[$i+10] = "Present";

        return $excell_header;
    }



    public function map($emp): array
    {
        // if($this->excel_row_counter > 85 ){// $this->list_of_emp->count()+1){
        //     dd($this->excel_row_counter,$this->list_of_emp->count());
        //     return ['Project Name','Project Color'];
        // }

        $attendence = (new EmployeeAttendanceDataService())->getAnEmployeeMonthlyAttendanceDateToDateRecordsWithProjecColorCodeForShowingAllMonthReport($emp->emp_auto_id,$this->start_day,$this->end_day, $this->month, $this->year);


        $records =  array_fill(0, $this->numberOfDaysInThisMonth + 9, 0); // first 6 colum & last 3 colum
        $counter= 0;

        $this->list_of_emp[$this->excel_row_counter++]->attendance_records =  $attendence;
        // $this->excel_row_counter++;

        $records[0] = $emp->employee_id;
        $records[1] = $emp->employee_name;
        $records[2] = $emp->akama_no;
        $records[3] = $emp->hourly_employee == 1 ? 'Hourly':'Basic';
        $records[4] = $emp->catg_name;
        $records[5] = $this->month."/".$this->year;

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
                $records[$i+5] = $arecord->attendance_status == "AW" ? ("".$arecord->daily_work_hours+$arecord->over_time) : $arecord->attendance_status;
                $total_overtime += (float) $arecord->over_time;
                $total_work_hours += (float) $arecord->daily_work_hours;
                $counter +=1;
            }


        }
        // $records[$this->numb

        $records[$this->numberOfDaysInThisMonth+6] = $total_work_hours+$total_overtime;
        $records[$this->numberOfDaysInThisMonth+7] = $total_work_hours;
        $records[$this->numberOfDaysInThisMonth+8] =   $total_overtime;
        $records[$this->numberOfDaysInThisMonth+9] = $this->numberOfDaysInThisMonth - $attendence->count();
        $records[$this->numberOfDaysInThisMonth+10] = $attendence->count();

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


    public function getExcellColumnName($index){

        switch($index){

            case 1:
                return "G";
            case 2:
                return "H";
            case 3:
                return "I";
            case 4:
                return "J";
            case 5:
                return "K";
            case 6:
                return "L";
            case 7:
                return "M";
            case 8:
                return "N";
            case 9:
                return "O";
            case 10:
                return "P";
            case 11:
                return "Q";
            case 12:
                return "R";
            case 13:
                return "S";
            case 14:
                return "T";
            case 15:
                return "U";
            case 16:
                return "V";
            case 17:
                return "W";
            case 18:
                return "X";
            case 19:
                return "Y";
            case 20:
                return "Z";
            case 21:
                return "AA";
            case 22:
                return "AB";
            case 23:
                return "AC";
            case 24:
                return "AD";
            case 25:
                return "AE";
            case 26:
                return "AF";
            case 27:
                return "AG";
            case 28:
                return "AH";
            case 29;
                return "AI";
            case 30:
                return "AJ";
            case 31:
                return "AK";
        }
    }
}
