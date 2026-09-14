<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProjectInfo;
use App\Enums\MonthsEnum;
use App\Enums\MonthlyAttendanceStatusEnum;

class AttendanceApprovalRecord extends Model
{
    use HasFactory;

    protected $casts = [
        'month' =>  MonthsEnum::Class,
        'approval_status' => MonthlyAttendanceStatusEnum::class
      ];

    public function project(){
        return $this->belongsTo(ProjectInfo::class, 'project_id','proj_id');
    }
}
