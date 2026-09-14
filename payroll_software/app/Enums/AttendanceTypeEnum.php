<?php


namespace App\Enums;

Enum AttendanceTypeEnum:string{
   case Working = "AW";
   case TravelLeave ="TL";
   case SickLeave ="SL";
   case Holiday="PH";
   case Bad_Weather="BW";
   case No_Work_In_Project ="NW";
}

//  get  all enums keys and value,  $type = AttendanceStatusEnum::cases();