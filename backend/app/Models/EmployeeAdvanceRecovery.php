<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeAdvanceRecovery extends Model
{
    /**
     * Installment deducted from a generated salary.
     */
    public const TYPE_SALARY = 'Salary';

    /**
     * Cash paid back by the employee.
     */
    public const TYPE_CASH = 'Cash';

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'recovery_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function advance(): BelongsTo
    {
        return $this->belongsTo(EmployeeAdvance::class, 'employee_advance_id');
    }

    /**
     * The cash/bank account a cash repayment was received into.
     */
    public function paymentAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'payment_account_id');
    }

    public function salaryHistory(): BelongsTo
    {
        return $this->belongsTo(SalaryHistory::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
