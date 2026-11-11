<?php

use App\Http\Controllers\Api\Inventory\AssetController;
use App\Http\Controllers\Api\Inventory\EmployeeInventoryController;
use App\Http\Controllers\Api\Inventory\InventoryDisposalController;
use App\Http\Controllers\Api\Inventory\InventoryItemController;
use App\Http\Controllers\Api\Inventory\InventoryStockController;
use App\Http\Controllers\Api\Inventory\InventoryTransferController;
use App\Http\Controllers\Api\Inventory\InventoryWarehouseController;
use App\Http\Controllers\Api\Inventory\MaterialRequestController;
use App\Http\Controllers\Api\Inventory\PurchaseOrderController;
use App\Http\Controllers\Api\Inventory\PurchaseReceiptController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Inventory API
|--------------------------------------------------------------------------
|
| Add your existing authentication middleware here if required.
| Example:
| Route::middleware('auth:sanctum')->prefix('inventory')->group(...)
|
*/

Route::prefix('inventory')->group(function () {

    // Master
    Route::get('items', [InventoryItemController::class, 'index']);
    Route::post('items', [InventoryItemController::class, 'store']);
    Route::get('items/{inventoryItem}', [InventoryItemController::class, 'show']);

    Route::get('warehouses', [InventoryWarehouseController::class, 'index']);
    Route::post('warehouses', [InventoryWarehouseController::class, 'store']);
    Route::get('warehouses/{inventoryWarehouse}', [InventoryWarehouseController::class, 'show']);

    // Material Requests
    Route::get('material-requests', [MaterialRequestController::class, 'index']);
    Route::post('material-requests', [MaterialRequestController::class, 'store']);
    Route::get('material-requests/{materialRequest}', [MaterialRequestController::class, 'show']);
    Route::post('material-requests/{materialRequest}/approve', [MaterialRequestController::class, 'approve']);

    // Purchase Orders
    Route::get('purchase-orders', [PurchaseOrderController::class, 'index']);
    Route::post('purchase-orders', [PurchaseOrderController::class, 'store']);
    Route::get('purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'show']);

    // Purchase Receiving
    Route::get('purchase-receipts/{purchaseReceipt}', [PurchaseReceiptController::class, 'show']);
    Route::post('purchase-receipts/{purchaseReceipt}/receive', [PurchaseReceiptController::class, 'receive']);

    // Warehouse Transfers
    Route::get('transfers', [InventoryTransferController::class, 'index']);
    Route::post('transfers', [InventoryTransferController::class, 'store']);
    Route::get('transfers/{inventoryTransfer}', [InventoryTransferController::class, 'show']);
    Route::post('transfers/{inventoryTransfer}/approve', [InventoryTransferController::class, 'approve']);
    Route::post('transfers/{inventoryTransfer}/dispatch', [InventoryTransferController::class, 'dispatch']);
    Route::post('transfers/{inventoryTransfer}/receive', [InventoryTransferController::class, 'receive']);

    // Stock & Audit
    Route::get('stocks', [InventoryStockController::class, 'index']);
    Route::get('transactions', [InventoryStockController::class, 'transactions']);

    // Employee Inventory
    Route::get('employee-inventory', [EmployeeInventoryController::class, 'index']);
    Route::post('employee-inventory/issue', [EmployeeInventoryController::class, 'issue']);
    Route::get('employee-inventory/{employeeInventory}', [EmployeeInventoryController::class, 'show']);
    Route::post('employee-inventory/{employeeInventory}/return', [EmployeeInventoryController::class, 'return']);

    // Assets
    Route::get('assets', [AssetController::class, 'index']);
    Route::get('assets/{inventoryAsset}', [AssetController::class, 'show']);
    Route::post('assets/{inventoryAsset}/assign', [AssetController::class, 'assign']);
    Route::post('asset-assignments/{assetAssignment}/return', [AssetController::class, 'return']);

    // Disposal
    Route::get('disposals', [InventoryDisposalController::class, 'index']);
    Route::post('disposals', [InventoryDisposalController::class, 'store']);
    Route::post('disposals/{inventoryDisposal}/approve', [InventoryDisposalController::class, 'approve']);
    Route::post('disposals/{inventoryDisposal}/dispose', [InventoryDisposalController::class, 'dispose']);
});
