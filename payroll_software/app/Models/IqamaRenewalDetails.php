<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IqamaRenewalDetails extends Model
{
    use HasFactory;
    protected $table = 'iqama_renewal_details';
    protected $primaryKey = 'IqamaRenewId';
    protected $guarded = [];

    protected $fillable = [
        'EmplId',
        'jawazat_fee',
        'maktab_alamal_fee',
        'bd_amount',
        'medical_insurance',
        'others_fee',
        'Year',
        'Cost7',
        'Cost8',
        'jawazat_penalty',
        'duration',
        'renewal_date',
        'remarks',
        'total_amount',
        'payment_number',
        'payment_date',
        'reference_emp_id',
        'renewal_status',
        'expense_paid_by',
        'iqama_expire_date',
        'inserted_by',
        'update_by',
        'payment_purpose_id',
        'extra_fee'
    ];

    public function employee()
    {
        return $this->belongsTo('App\Models\EmployeeInfo', 'EmplId', 'emp_auto_id');
    }
}
