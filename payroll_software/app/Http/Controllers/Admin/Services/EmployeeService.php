<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Models\EmployeeInfo;


class EmployeeService extends Controller
{
    public function getTotalAllActiveInactiveEmployees($statusId)
    {
        if ($statusId == 0) {
            return EmployeeInfo::count();
        } else {
            return EmployeeInfo::where('job_status', $statusId)->count();
            // dd(count($list));

        }
    }
}
