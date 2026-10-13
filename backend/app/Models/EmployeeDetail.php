<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeDetail extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    public const PAYMENT_METHODS = ['Cash', 'Bank'];

    /**
     * The columns this table owns, used to split employee form input.
     *
     * @var array<int, string>
     */
    public const FIELDS = [
        'national_id', 'passport_no', 'marital_status', 'blood_group', 'permanent_address',
        'permanent_division_id', 'permanent_district_id', 'permanent_upazila_id', 'payment_method',
        'emergency_contact_name', 'emergency_contact_relation', 'emergency_contact_phone', 'notes',
    ];

    protected $table = 'emp_details';

    protected $guarded = [];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function permanentDivision(): BelongsTo
    {
        return $this->belongsTo(Division::class, 'permanent_division_id');
    }

    public function permanentDistrict(): BelongsTo
    {
        return $this->belongsTo(District::class, 'permanent_district_id');
    }

    public function permanentUpazila(): BelongsTo
    {
        return $this->belongsTo(Upazila::class, 'permanent_upazila_id');
    }
}
