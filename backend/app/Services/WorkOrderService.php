<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\WorkOrder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class WorkOrderService
{
    public function paginate(int $perPage = 15, ?string $search = null, ?int $customerId = null, ?string $status = null): LengthAwarePaginator
    {
        return WorkOrder::query()
            ->with('customer:id,name')
            ->withPaymentTotals()
            ->when($search, fn ($query) => $query->search($search))
            ->when($customerId, fn ($query) => $query->where('customer_id', $customerId))
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest('issue_date')
            ->latest('id')
            ->paginate($perPage);
    }

    public function find(WorkOrder $workOrder): WorkOrder
    {
        return $workOrder->load('customer')->loadPaymentTotals();
    }

    /**
     * Every work order of a customer with its paid and outstanding amounts,
     * for picking one on a sale.
     *
     * @return Collection<int, WorkOrder>
     */
    public function forCustomer(Customer $customer): Collection
    {
        return $customer->workOrders()
            ->withPaymentTotals()
            ->latest('issue_date')
            ->latest('id')
            ->get();
    }

    /**
     * @param  array{customer_id: int, work_title: string, work_order_no: string, issue_date: string, total_amount: float, retention_percent?: ?float, deliver_date?: ?string, status?: ?string}  $data
     */
    public function create(array $data): WorkOrder
    {
        $workOrder = WorkOrder::create([
            ...$data,
            'retention_percent' => $data['retention_percent'] ?? 0,
            'status' => $data['status'] ?? 'Pending',
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return $workOrder->load('customer')->loadPaymentTotals();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(WorkOrder $workOrder, array $data): WorkOrder
    {
        $workOrder->fill([...$data, 'updated_by' => Auth::id()]);
        $workOrder->save();

        return $workOrder->load('customer')->loadPaymentTotals();
    }

    public function delete(WorkOrder $workOrder): void
    {
        $workOrder->delete();
    }
}
