<?php

namespace App\Enums;

Enum BdOfficePaymentStatusEnum:Int{

    case Approval_Pending = 1;
    case Approved = 5;
    case Rejected = 10;
    case Completed = 15;
}





