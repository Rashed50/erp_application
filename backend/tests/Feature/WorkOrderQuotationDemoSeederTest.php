<?php

use App\Models\Quotation;
use App\Models\WorkOrder;
use Database\Seeders\WorkOrderQuotationDemoSeeder;

it('seeds repeatable sample quotations linked to work orders', function () {
    $this->seed(WorkOrderQuotationDemoSeeder::class);
    $this->seed(WorkOrderQuotationDemoSeeder::class);

    expect(Quotation::query()->whereIn('invoice_number', ['DEMO-Q-001', 'DEMO-Q-002', 'DEMO-Q-003'])->count())->toBe(3)
        ->and(WorkOrder::query()->whereIn('work_order_no', ['DEMO-WO-001', 'DEMO-WO-002', 'DEMO-WO-003'])->count())->toBe(3);

    $workOrder = WorkOrder::query()->where('work_order_no', 'DEMO-WO-001')->firstOrFail();
    expect($workOrder->quotation_id)->toBe(
        Quotation::query()->where('invoice_number', 'DEMO-Q-001')->value('id'),
    );
});
