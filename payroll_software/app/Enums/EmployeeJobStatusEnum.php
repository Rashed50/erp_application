<?php

namespace App\Enums;



enum EmployeeJobStatusEnum: int
{
  case ApprovalPending = 0;
  case Active = 1;
  case InActive = 2;
  case Final_Exit = 3;
  case Release = 4;
  case Vacation = 5;
  case Run_Away = 6;
  case VISA_Cancel = 7;
  case Final_Exit_and_Return_Back = 8;
}
