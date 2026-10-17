<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CustomerWorkOrderSeeder extends Seeder
{
    public function run(): void
    {
        // Use the first user as the creator/approver (fallback to null)
        $userId = User::query()->value('id');

        // Wipe existing data (safe for dev seeding)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        WorkOrder::query()->forceDelete(); // respects soft deletes
        Customer::query()->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $customers = [
            [
                'name'                    => 'Al Noor Trading Co.',
                'email'                   => 'info@alnoor-trading.com',
                'phone'                   => '+966501234001',
                'address'                 => 'King Fahd Road, Riyadh 12211, Saudi Arabia',
                'vat_no'                  => '300123456700003',
                'payment_term'            => 30,
                'contact_person'          => 'Ahmed Al-Qahtani',
                'contact_person_phone'    => '+966501234011',
                'contact_person_email'    => 'ahmed@alnoor-trading.com',
                'country'                 => 'Saudi Arabia',
                'opening_date'            => '2024-01-15',
                'opening_balance'         => 25000.00,
                'current_balance'         => 25000.00,
                'active_status'           => true,
                'branch_office_id'        => null,
                'created_by'              => $userId,
                'updated_by'              => $userId,
            ],
            [
                'name'                    => 'Saudi Build Solutions LLC',
                'email'                   => 'accounts@saudibuild.sa',
                'phone'                   => '+966501234002',
                'address'                 => 'Al Olaya District, Riyadh 12611, Saudi Arabia',
                'vat_no'                  => '300223456700003',
                'payment_term'            => 45,
                'contact_person'          => 'Faisal Al-Harbi',
                'contact_person_phone'    => '+966501234022',
                'contact_person_email'    => 'faisal@saudibuild.sa',
                'country'                 => 'Saudi Arabia',
                'opening_date'            => '2024-03-01',
                'opening_balance'         => 0.00,
                'current_balance'         => 18500.50,
                'active_status'           => true,
                'branch_office_id'        => null,
                'created_by'              => $userId,
                'updated_by'              => $userId,
            ],
            [
                'name'                    => 'Gulf Engineering Works',
                'email'                   => 'contact@gulfeng.com',
                'phone'                   => '+971501234003',
                'address'                 => 'Sheikh Zayed Road, Dubai, UAE',
                'vat_no'                  => '100123456700003',
                'payment_term'            => 20,
                'contact_person'          => 'Omar Al-Mansoori',
                'contact_person_phone'    => '+971501234033',
                'contact_person_email'    => 'omar@gulfeng.com',
                'country'                 => 'UAE',
                'opening_date'            => '2024-05-10',
                'opening_balance'         => 12000.00,
                'current_balance'         => 12000.00,
                'active_status'           => true,
                'branch_office_id'        => null,
                'created_by'              => $userId,
                'updated_by'              => $userId,
            ],
            [
                'name'                    => 'Al Faisal Contracting Est.',
                'email'                   => 'billing@alfaisal-contract.com',
                'phone'                   => '+966501234004',
                'address'                 => 'Dammam Port Road, Dammam 32241, Saudi Arabia',
                'vat_no'                  => '300323456700003',
                'payment_term'            => 60,
                'contact_person'          => 'Khalid Al-Dossari',
                'contact_person_phone'    => '+966501234044',
                'contact_person_email'    => 'khalid@alfaisal-contract.com',
                'country'                 => 'Saudi Arabia',
                'opening_date'            => '2023-11-20',
                'opening_balance'         => 50000.00,
                'current_balance'         => 32000.00,
                'active_status'           => true,
                'branch_office_id'        => null,
                'created_by'              => $userId,
                'updated_by'              => $userId,
            ],
            [
                'name'                    => 'Najd Industrial Services',
                'email'                   => 'finance@najd-industrial.sa',
                'phone'                   => '+966501234005',
                'address'                 => 'Industrial City, Jubail 31961, Saudi Arabia',
                'vat_no'                  => '300423456700003',
                'payment_term'            => 30,
                'contact_person'          => 'Sultan Al-Otaibi',
                'contact_person_phone'    => '+966501234055',
                'contact_person_email'    => 'sultan@najd-industrial.sa',
                'country'                 => 'Saudi Arabia',
                'opening_date'            => '2024-07-01',
                'opening_balance'         => 0.00,
                'current_balance'         => 0.00,
                'active_status'           => true,
                'branch_office_id'        => null,
                'created_by'              => $userId,
                'updated_by'              => $userId,
            ],
        ];

        foreach ($customers as $index => $data) {
            $customer = Customer::create($data);

            $this->createWorkOrdersForCustomer($customer, $userId, $index + 1);
        }

        $this->command->info('Seeded ' . count($customers) . ' customers and ' . (count($customers) * 2) . ' work orders.');
    }

    private function createWorkOrdersForCustomer(Customer $customer, ?int $userId, int $seq): void
    {
        $workOrders = [
            [
                'work_title'        => 'Supply of Structural Steel – Phase 1',
                'work_order_no'     => sprintf('WO-%s-001', str_pad((string) $seq, 4, '0', STR_PAD_LEFT)),
                'issue_date'        => Carbon::now()->subDays(60)->toDateString(),
                'total_amount'      => 125000.00,
                'retention_percent' => 10.00,
                'deliver_date'      => Carbon::now()->addDays(30)->toDateString(),
                'status'            => 'Approved',
                'approved_by'       => $userId,
                'approved_at'       => Carbon::now()->subDays(58),
            ],
            [
                'work_title'        => 'Mechanical Installation – Building B',
                'work_order_no'     => sprintf('WO-%s-002', str_pad((string) $seq, 4, '0', STR_PAD_LEFT)),
                'issue_date'        => Carbon::now()->subDays(15)->toDateString(),
                'total_amount'      => 87500.50,
                'retention_percent' => 5.00,
                'deliver_date'      => Carbon::now()->addDays(45)->toDateString(),
                'status'            => 'Pending',
                'approved_by'       => null,
                'approved_at'       => null,
            ],
        ];

        foreach ($workOrders as $wo) {
            $customer->workOrders()->create(array_merge($wo, [
                'created_by' => $userId,
                'updated_by' => $userId,
            ]));
        }
    }
}