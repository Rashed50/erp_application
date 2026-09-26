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
        'payment_method', 'bank_name', 'bank_branch', 'bank_account_name', 'bank_account_no',
        'emergency_contact_name', 'emergency_contact_relation', 'emergency_contact_phone', 'notes',
    ];

    protected $table = 'emp_details';

    protected $guarded = [];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
