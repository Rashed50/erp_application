<?php

namespace App\Http\Controllers\Admin\Helper;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JobStatus;
use DateTime;
use Carbon\Carbon;

class HelperController extends Controller
{
  public function getTodayDate(){
    return date("Y/m/d");
  }
  // get month name for integer value
  public function getMonthName($monthId)
  {
    $dateObj   = DateTime::createFromFormat('!m', $monthId);
    return $monthName = $dateObj->format('F');
  }
  
  public static function getMonthNameByStaticMethod($monthId)
  {
    $dateObj   = DateTime::createFromFormat('!m', $monthId);
    return  $dateObj->format('F');
  }

  public function getYear()
  {
    return $pdate = date('Y');
  }

  public function getEmployeeStatus()
  {

     // return $status = JobStatus::all();
     return ["Active","Inactive","Final Exit","Relase","Vacation","Runaway"];
                     
  }

  public function getNextFirdayDate($day, $month, $year)
  {

    $date = $year . '-' . $month . '-' . $day;
    $date    =  new DateTime($date);
    $day = $date->format('l');  //pass
    return $day;
  }
  public function getNumberOfDaysInMonthAndYear($month, $year)
  {
    $noOfDaysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    if ($noOfDaysInMonth == null) {
      return 0;
    } else {
      return $noOfDaysInMonth;
    }
  }

 public function countTotalHolidayInThisMonth($month,$year){

    $days = $this->getNumberOfDaysInMonthAndYear($month,$year);
    $holidayArray = array_fill(1, 31, 0);
    $totalHolidays = 0;
    for ($i = 1; $i <= $days; $i++) {
      $date = $year . '-' . $month . '-' . $i;
      $date    =  new DateTime($date);
      $day = $date->format('l');  
      if ($day == "Friday") {
        $totalHolidays++;
        $holidayArray[$i] = 1;
      }
    }
    return [$totalHolidays,$holidayArray];

  }
  public function getAMonthHolidaysAsArray($month,$year){

        $days = $this->getNumberOfDaysInMonthAndYear($month,$year);
        $holidayArray = array();
        $j= 0;

        for ($i = 1; $i <= $days; $i++) {
            $date = $year . '-' . $month . '-' . $i;
            $date    =  new DateTime($date);
            $day = $date->format('l');
            if ($day == "Friday") {

                $holidayArray[$j++] = $i;
            }
        }
        return $holidayArray;

  }
  
    public function checkThisDayIsFriday($date){

      $date    =  new DateTime($date);
      $day = $date->format('l');  
      if ($day == "Friday") {
          return true;
      }  
      return  false;

  }
  
   public function getAllDaysNameInMonth($month,$year){

    $day_name_in_month = array();
    $total_day = $this->getNumberOfDaysInMonthAndYear($month,$year);
    for($c = 1; $c <= $total_day; $c ++){

      $date = $year."-".$month."-".$c;
      $day_name_in_month[$c] =  substr((new DateTime($date))->format('l'),0,2);      
    }
    return $day_name_in_month;
  }

  public function getMonthFromDateValue($dateValue)
  {
    if ($dateValue == null) {
      return 0;
    }
    $time = strtotime($dateValue);
    return  $month = (int) date("m", $time);
  }
  
   public function getCurrentMonthIntValue()
  {
     
    $time = strtotime(date("Y/m/d"));
    return  $month = (int) date("m", $time);
  }


  public function getYearFromDateValue($dateValue)
  {
    if ($dateValue == null) {
      return 0;
    }
    $time = strtotime($dateValue);
    return $year = (int) date("Y", $time);
  }
  public function getDayFromDateValue($dateValue)
  {
    if ($dateValue == null) {
      return 0;
    }
    $time = strtotime($dateValue);
    return (int) date("d", $time);
  }
  
  public function getTodayDayFromCurrentMonth()
  {
    $time = strtotime(date("Y/m/d"));
    return  (int) date("d", $time);
  }

   public function getDayMonthAndYearFromDateValue($dateValue)
  {
    if ($dateValue == null || $dateValue == "") {
      return [0,0,0];
    }
     $time = strtotime($dateValue);
     $day =  (int) date("d", $time);
     $month =  (int) date("m", $time);
     $year =  (int) date("Y", $time);
     return [$day,$month,$year];
  }
  
    function getLastDateFromDateValue($a_date){
       return date("Y-m-t", strtotime($a_date));
  }


  function getMonthsInRangeOfDate($startDate, $endDate)
  {
    $months = array();

    while (strtotime($startDate) <= strtotime($endDate)) {
      $months[] = array(
        'year' => (int) date('Y', strtotime($startDate)),
        'month' => (int) date('m', strtotime($startDate)),
      );

       
      $startDate = date('01 M Y', strtotime($startDate . '+ 1 month'));
    }

    return $months;
  }
  
    function getListOfMonthInRangeOfTwoDate($startDate, $endDate)
  {
    $months = array();
    $counter =0;

    while (strtotime($startDate) <= strtotime($endDate)) {
      
        $months[$counter++] =  (int) date('m', strtotime($startDate));     
      // Set date to 1 so that new month is returned as the month changes.
      $startDate = date('01 M Y', strtotime($startDate . '+ 1 month'));
    }

    return $months;
  }

  

  // File Extension Check
    public function checkUploadedFileProperties($extension, $fileSize)
    {
            $valid_extension = array("csv", "xlsx"); //Only want csv and excel files
            $maxFileSize = 5242888; // Uploaded file size limit is 5mb
            if (in_array(strtolower($extension), $valid_extension)) {
                if ($fileSize <= $maxFileSize) {
                    return true;
                } else {
                     return false;
                }
            } else {
               return false;
            }
    }

    // File Extension Check
    public function checkUploadedFileFormatAndUploadFileSize($extension, $fileSize)
    {
            $valid_extension = array("csv", "xlsx"); //Only want csv and excel files
            $maxFileSize = 5242888; // Uploaded file size limit is 5mb
            if (in_array(strtolower($extension), $valid_extension)) {
                if ($fileSize <= $maxFileSize) {
                    return true;
                } else {
                     return false;
                }
            } else {
               return false;
            }
    }

  
 public function numberToWord($num = '')
{
    $num    = ( string ) ( ( int ) $num );
    
    if( ( int ) ( $num ) && ctype_digit( $num ) )
    {
        $words  = array( );
         
        $num    = str_replace( array( ',' , ' ' ) , '' , trim( $num ) );
         
        $list1  = array('','one','two','three','four','five','six','seven',
            'eight','nine','ten','eleven','twelve','thirteen','fourteen',
            'fifteen','sixteen','seventeen','eighteen','nineteen');
         
        $list2  = array('','ten','twenty','thirty','forty','fifty','sixty',
            'seventy','eighty','ninety','hundred');
         
        $list3  = array('','thousand','million','billion','trillion',
            'quadrillion','quintillion','sextillion','septillion',
            'octillion','nonillion','decillion','undecillion',
            'duodecillion','tredecillion','quattuordecillion',
            'quindecillion','sexdecillion','septendecillion',
            'octodecillion','novemdecillion','vigintillion');
         
        $num_length = strlen( $num );
        $levels = ( int ) ( ( $num_length + 2 ) / 3 );
        $max_length = $levels * 3;
        $num    = substr( '00'.$num , -$max_length );
        $num_levels = str_split( $num , 3 );
         
        foreach( $num_levels as $num_part )
        {
            $levels--;
            $hundreds   = ( int ) ( $num_part / 100 );
            $hundreds   = ( $hundreds ? ' ' . $list1[$hundreds] . ' Hundred' . ( $hundreds == 1 ? '' : 's' ) . ' ' : '' );
            $tens       = ( int ) ( $num_part % 100 );
            $singles    = '';
             
            if( $tens < 20 ) { $tens = ( $tens ? ' ' . $list1[$tens] . ' ' : '' ); } else { $tens = ( int ) ( $tens / 10 ); $tens = ' ' . $list2[$tens] . ' '; $singles = ( int ) ( $num_part % 10 ); $singles = ' ' . $list1[$singles] . ' '; } $words[] = $hundreds . $tens . $singles . ( ( $levels && ( int ) ( $num_part ) ) ? ' ' . $list3[$levels] . ' ' : '' ); } $commas = count( $words ); if( $commas > 1 )
        {
            $commas = $commas - 1;
        }
         
        $words  = implode( ', ' , $words );
         
        $words  = trim( str_replace( ' ,' , ',' , ucwords( $words ) )  , ', ' );
        if( $commas )
        {
            $words  = str_replace( ',' , ' and' , $words );
        }
         
        return $words;
    }
    else if( ! ( ( int ) $num ) )
    {
        return 'Zero';
    }
    return '';
}

  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
}
