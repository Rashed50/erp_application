<?php

namespace App\Models\Enum;



enum JobStatusEnum: int
{
  case ApprovalPending = 0;
  case Active = 1;
  case InActive = 2;
  case Final_Exit = 3;
  case Release = 4;
  case Vacation = 5;
  case Run_Away = 6;
}
