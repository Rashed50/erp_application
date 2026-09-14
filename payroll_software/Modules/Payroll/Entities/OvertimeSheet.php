<?php

namespace Modules\Payroll\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OvertimeSheet extends Model
{
    protected $table = 'overtime_sheets';
    protected $primaryKey = 'ots_auto_id';

    protected $fillable = [
        'project_id',
        'ot_date',
        'approved_by',
        'insert_by',
        'ot_file'
    ];
}
