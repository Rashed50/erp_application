<?php

namespace Modules\Subcontractor\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;


class SubcontractorPayment extends Model
{
    use HasFactory;

    protected $table = 'subcontractors_payment';
    protected $primaryKey = 'subcon_pay_auto_id';

    protected $fillable = [
        'subcon_auto_id',
        'total_amount',
        'discount',
        'grand_total',
        'month',
        'year',
        'payment_date',
        'payment_method',
        'remarks',
        'payment_file',
        'approved_by',
        'created_by',
        'updated_by',
        //act_status  0 = created, 3 review, 10 final aceptance and approved
    ];

    public function subcontractor()
    {
        return $this->belongsTo(SubcontractorInfo::class, 'subcon_auto_id', 'subcon_auto_id');
    }


    public function approvedUser()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function createdUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedUser()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    protected static function newFactory()
    {
        return \Modules\Subcontractor\Database\factories\SubcontractorPaymentFactory::new();
    }
}
