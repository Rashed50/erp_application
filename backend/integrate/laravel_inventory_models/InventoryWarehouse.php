<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class InventoryWarehouse extends Model
{
    use HasFactory;
    protected $fillable = [
        'warehouse_code',
        'name',
        'warehouse_type',
        'parent_id',
        'zone_id',
        'project_id',
        'manager_employee_auto_id',
        'address',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function parent(): BelongsTo { return $this->belongsTo(self::class, 'parent_id'); }
    public function children(): HasMany { return $this->hasMany(self::class, 'parent_id'); }
    public function project(): BelongsTo { return $this->belongsTo(ProjectInfo::class, 'project_id', 'proj_id'); }
    public function manager(): BelongsTo { return $this->belongsTo(EmployeeInfo::class, 'manager_employee_auto_id', 'emp_auto_id'); }
    public function stocks(): HasMany { return $this->hasMany(InventoryStock::class, 'warehouse_id'); }
    public function incomingTransfers(): HasMany { return $this->hasMany(InventoryTransfer::class, 'to_warehouse_id'); }
    public function outgoingTransfers(): HasMany { return $this->hasMany(InventoryTransfer::class, 'from_warehouse_id'); }
    public function purchaseReceipts(): HasMany { return $this->hasMany(PurchaseReceipt::class, 'warehouse_id'); }
    public function transactionsFrom(): HasMany { return $this->hasMany(InventoryTransaction::class, 'from_location_id'); }
    public function transactionsTo(): HasMany { return $this->hasMany(InventoryTransaction::class, 'to_location_id'); }
    public function employeeInventory(): HasMany { return $this->hasMany(EmployeeInventory::class, 'warehouse_id'); }
    public function assets(): HasMany { return $this->hasMany(InventoryAsset::class, 'current_warehouse_id'); }
    public function assetAssignments(): HasMany { return $this->hasMany(AssetAssignment::class, 'warehouse_id'); }
    public function assetReturns(): HasMany { return $this->hasMany(AssetReturn::class, 'destination_warehouse_id'); }
    public function disposals(): HasMany { return $this->hasMany(InventoryDisposal::class, 'warehouse_id'); }

}
