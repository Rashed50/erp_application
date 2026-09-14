<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceRecords extends Model
{
    use HasFactory;

    public function mainContractor(){
        return $this->belongsTo(MainContractorInfo::class, 'main_contractor_id', 'mc_auto_id');
    }

    public function subContractor(){
        return $this->belongsTo(SubCompanyInfo::class, 'sub_contractor_id', 'sb_comp_id');
    }

    public function bankInfo(){
        return $this->belongsTo(BankDetails::class, 'bank_details_id', 'id');
    }

    public function SubmitedBy(){
        return $this->belongsTo(User::class, 'entered_by_id', 'emp_auto_id');
    }

}
