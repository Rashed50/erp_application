<?php

namespace Modules\Subcontractor\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class SubcontractorService extends Model
{
    use HasFactory;


    protected $table = 'subcontractor_services'; // Ensure table name matches your migration

    protected $primaryKey = 'subcon_service_auto_id'; // If not 'id', define your primary key

    protected $fillable = [
        'subcon_auto_id',
        'no_of_unit',
        'per_unit_rate',
        'total_amount',
        'discount',
        'grand_total',
        'month',
        'year',
        'invoice_date',
        'invoice_no',
        'service_type',
        'service_invoice',
        'remarks',
        'srv_status',
        'approved_by',
        'created_by',
        'updated_by',
    ];

    public $timestamps = true; // If using 'created_at' and 'updated_at'

    // Relationships (if needed)

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
        return \Modules\Subcontractor\Database\factories\SubcontractorServiceFactory::new();
    }
}
