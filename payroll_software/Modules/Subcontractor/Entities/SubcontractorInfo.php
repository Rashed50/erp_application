<?php

namespace Modules\Subcontractor\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubcontractorInfo extends Model
{
    use HasFactory;

    protected $table      = 'subcontractor_infos'; // Optional if table name follows Laravel naming convention
    protected $primaryKey = 'subcon_auto_id';

    protected $fillable = [
        'subcon_name',
        'sponsor_id',
        'id_number',
        'passfort_no',
        'pass_expire',
        'iqama_no',
        'iqama_expire',
        'mobile_no',
        'abshar_mobile_no',
        'country_contact_no',
        'country_id',
        'division_id',
        'district_id',
        'post_code',
        'details',
        'present_address',
        'subcont_email',
        'joining_date',
        'opening_balance',
        'entry_date',
        'remarks',
        'iqama_file',
        'passport_file',
        'contract_paper',
        'act_status',
        'approved_by',
        'created_by',
        'updated_by',
    ];

    protected static function newFactory()
    {
        return \Modules\Subcontractor\Database\factories\SubcontractorInfoFactory::new();
    }

    // In your SubcontractorInfo model
    public function services()
    {
        return $this->hasMany(SubcontractorService::class, 'subcon_auto_id', 'subcon_auto_id');
    }
}
