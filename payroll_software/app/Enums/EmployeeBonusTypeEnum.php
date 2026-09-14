<?php

namespace App\Enums;

Enum EmployeeBonusTypeEnum:int{
    case AnnualBonus = 5;
    case PerformanceBonus = 10;
    case FestivalBonus = 15;
    case Single_AirTicket  = 30;
    case Return_AirTicket  = 35;
    case Round_AirTicket = 37;
    case One_Month_Salary  = 40;
    case Leave_Salary  = 45;
    case Bonus_and_Air_Ticket  = 50;
}


