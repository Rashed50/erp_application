<?php

namespace Modules\Account\Models;

use App\Models\AccountsModule\ChartOfAccounts;
use App\Models\ProjectInfo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChartofaccSalesRecord extends Model
{
    use HasFactory;

    protected $table = "chartofacc_sales_records";
    protected $primaryKey = "sr_auto_id";

    protected $fillable = [
        'sr_auto_id',
        'cus_auto_id',
        'account_credit_id',
        'account_debit_id',
        'sr_invoice_no',
        'sr_invoice_description',
        'sr_tnx_id',
        'sr_issue_date',
        'sr_payment_terms',
        'sr_due_date',
        'sr_supply_date',
        'sr_payment_mean',
        'sr_total_amount',
        'sr_discount_amount',
        'sr_vat_amount',
        'sr_grand_total_amount',
        'sr_status',
        'retention_amount',
        'branch_office_id',
        'attachments',
        'notes',
        'created_by_id',
        'updated_by_id',
        'project_id',
        'created_at',
        'updated_at',
        'is_draft',
        'paid_type',
        'paid_date',

    ];
    protected $guarded = [];
    protected $casts = [
        'attachments' => 'array'
    ];

    static function getTableName()
    {
        return with(new static)->getTable();
    }

    function customer()
    {
        return $this->belongsTo(CustomerLedger::class, 'customer_id', 'cus_auto_id');
    }

    function items()
    {
        return $this->hasMany(ChartofaccSalesRecordDetail::class, 'sr_auto_id', 'sr_auto_id');
    }

    // function debit()
    // {
    //     return $this->belongsTo(JournalInfo::class, 'account_debit_id', 'jour_id');
    // }

    function debit()
    {
        return $this->belongsTo(ChartOfAccounts::class, 'account_debit_id', 'chart_of_acct_id');
    }

    function credit()
    {
        return $this->belongsTo(ChartOfAccounts::class, 'account_credit_id', 'chart_of_acct_id');
    }

    function ProjectDetails()
    {
        return $this->belongsTo(ProjectInfo::class, 'project_id', 'proj_id');
    }

}
