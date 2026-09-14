<?php

namespace Modules\Account\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\AccountsModule\ChartOfAccounts;
use App\Models\{InventorySupplier, User,ProjectInfo};
use Modules\Account\Models\{PurchaseInvoiceDetails, PurchaseInvoiceAttachment,SupplierLedger};

class PurchaseInvoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'purchase_invoice';
    protected $primaryKey = 'pur_id';

    protected $fillable = [
        'purchase_type',
        'description',
        'supplier_id',
        'invoice_number',
        'issue_date',
        'purchase_date',
        'total_amount',
        'vat_amount',
        'discount_amount',
        'net_total',
        'notes',
        'project_id',
        'account_debit_id',
        'account_credit_id',
        'created_by',
        'updated_by',
        'branch_id',
    ];


    public function project()
    {
        return $this->belongsTo(ProjectInfo::class, 'project_id','proj_id');
    }

    public function supplier()
    {
        return $this->belongsTo(SupplierLedger::class, 'supplier_id');
    }

    public function details()
    {
        return $this->hasMany(PurchaseInvoiceDetails::class, 'purchase_id');
    }

    public function attachments()
    {
        return $this->hasMany(PurchaseInvoiceAttachment::class, 'p_invoice_id');
    }

    public function createdAdmin()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedAdmin()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    function debit()
    {
        return $this->belongsTo(ChartOfAccounts::class, 'account_debit_id', 'chart_of_acct_id');
    }

    function credit()
    {
        return $this->belongsTo(ChartOfAccounts::class, 'account_credit_id', 'chart_of_acct_id');
    }

}
