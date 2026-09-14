<?php

namespace App\Enums;

Enum RenewalPurposeEnum:Int{

    case Iqama_Renewal = 1;
    case Medical_Insurance = 2;
    case Exit_Re_Entry = 3;
    case Family_Iqama = 4;
    case Family_Medical_Insurance = 5;
    case Other = 6;
}
