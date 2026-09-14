<?php
    
namespace App\Enums;

Enum EmpWorkActivityRatingEnum:int
 {

    case UnDefined = 0;
    case Low = 5;
    case Medium = 10;
    case Good = 15;
    case Best = 20;
}
