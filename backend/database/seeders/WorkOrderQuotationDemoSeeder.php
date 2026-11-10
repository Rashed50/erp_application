<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Quotation;
use App\Models\WorkOrder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkOrderQuotationDemoSeeder extends Seeder
{
    /**
     * Add repeatable sample customers, quotations, and linked work orders.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $samples = [
                [
                    'customer' => ['name' => 'Demo Construction Ltd.', 'email' => 'demo.workorders1@example.com'],
                    'quotation' => ['number' => 'DEMO-Q-001', 'title' => 'Office renovation', 'amount' => 125000],
                    'work_order' => ['number' => 'DEMO-WO-001', 'title' => 'Office renovation project', 'amount' => 125000, 'status' => 'In Progress'],
                ],
                [
                    'customer' => ['name' => 'Demo Engineering Co.', 'email' => 'demo.workorders2@example.com'],
                    'quotation' => ['number' => 'DEMO-Q-002', 'title' => 'Warehouse electrical installation', 'amount' => 87500.50],
                    'work_order' => ['number' => 'DEMO-WO-002', 'title' => 'Warehouse electrical installation', 'amount' => 87500.50, 'status' => 'Pending'],
                ],
                [
                    'customer' => ['name' => 'Demo Supplies Group', 'email' => 'demo.workorders3@example.com'],
                    'quotation' => ['number' => 'DEMO-Q-003', 'title' => 'Building materials supply', 'amount' => 64250],
                    'work_order' => ['number' => 'DEMO-WO-003', 'title' => 'Building materials supply', 'amount' => 64250, 'status' => 'Completed'],
                ],
            ];

            foreach ($samples as $index => $sample) {
                $customer = Customer::query()->firstOrCreate(
                    ['email' => $sample['customer']['email']],
                    [
                        'name' => $sample['customer']['name'],
                        'phone' => '+88017000000'.($index + 1),
                        'address' => 'Dhaka, Bangladesh',
                        'country' => 'Bangladesh',
                        'payment_term' => 30,
                        'opening_date' => today()->toDateString(),
                        'active_status' => true,
                    ],
                );

                $quotation = Quotation::query()->firstOrCreate(
                    ['invoice_number' => $sample['quotation']['number']],
                    [
                        'customer_id' => $customer->id,
                        'description' => $sample['quotation']['title'],
                        'issue_date' => today()->subDays(10 - $index)->toDateString(),
                        'total_amount' => $sample['quotation']['amount'],
                        'discount_amount' => 0,
                        'vat_amount' => 0,
                        'profit_percent' => 0,
                        'net_total' => $sample['quotation']['amount'],
                        'notes' => 'Sample quotation for Work Order testing.',
                    ],
                );

                WorkOrder::query()->firstOrCreate(
                    ['work_order_no' => $sample['work_order']['number']],
                    [
                        'customer_id' => $customer->id,
                        'quotation_id' => $quotation->id,
                        'work_title' => $sample['work_order']['title'],
                        'issue_date' => today()->subDays(8 - $index)->toDateString(),
                        'total_amount' => $sample['work_order']['amount'],
                        'retention_percent' => 5,
                        'deliver_date' => today()->addDays(30 + $index)->toDateString(),
                        'status' => $sample['work_order']['status'],
                    ],
                );
            }
        });

        $this->command?->info('Work Order demo data seeded (3 customers, 3 quotations, 3 work orders).');
    }
}
