<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeBankDetail extends Model
{
    use HasFactory;

    /**
     * The columns this table owns, used to split employee form input.
     *
     * @var array<int, string>
     */
    public const FIELDS = ['bank_name', 'branch_name', 'account_name', 'account_no', 'routing_no'];

    protected $table = 'emp_bank_details';

    protected $guarded = [];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
