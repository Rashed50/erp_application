<?php
    namespace App\Enums;

Enum EmployeeActivityTypeEnum:int {
    case Undefined_Activity = 0;
    case Job_Activity = 1;
    case Salary_Activity = 10;
    case Ilegal_Activity = 20;
     
}
