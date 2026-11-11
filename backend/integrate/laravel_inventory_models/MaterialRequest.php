<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MaterialRequest extends Model
{
    use HasFactory;
    protected $fillable = [
        'request_no',
        'project_id',
        'requested_by',
        'request_date',
        'required_date',
        'priority',
        'status',
        'remarks',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'request_date' => 'date',
        'required_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function project(): BelongsTo { return $this->belongsTo(ProjectInfo::class, 'project_id', 'proj_id'); }
    public function requester(): BelongsTo { return $this->belongsTo(EmployeeInfo::class, 'requested_by', 'emp_auto_id'); }
    public function approver(): BelongsTo { return $this->belongsTo(EmployeeInfo::class, 'approved_by', 'emp_auto_id'); }
    public function items(): HasMany { return $this->hasMany(MaterialRequestItem::class, 'material_request_id'); }
    public function purchaseOrders(): HasMany { return $this->hasMany(PurchaseOrder::class, 'material_request_id'); }

}
