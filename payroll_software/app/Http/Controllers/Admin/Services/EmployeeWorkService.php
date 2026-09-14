<?php

namespace App\Http\Controllers\Admin\Services;

use App\Http\Controllers\Controller;
use App\Models\MonthlyWorkHistory;

class EmployeeWorkService extends Controller
{
    public function getTotalWorkingEmployees($month, $year)
    {
        return MonthlyWorkHistory::where('month_id', $month)->Where('year_id', $year)->count();
    }
}
