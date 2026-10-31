<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('customers')->insert([
            [
                'name'                 => 'ABC Corporation',
                'email'                => 'info1@abccorp.com',
                'phone'                => '+8801700000001',
                'address'              => 'Dhaka, Bangladesh',
                'vat_no'               => 'VAT123456789',
                'payment_term'         => 20,
                'contact_person'       => 'Mr. John Doe',
                'contact_person_phone' => '+8801800000001',
                'contact_person_email' => 'john@abccorp.com',
                'country'              => 'Bangladesh',
                'opening_date'         => Carbon::now()->subMonths(2)->toDateString(),
                'opening_balance'      => 0.00,
                'current_balance'      => 0.00,
                'active_status'        => 1,
                'branch_office_id'     => null,
                'created_at'           => Carbon::now(),
                'updated_at'           => Carbon::now(),
            ],
            [
                'name'                 => 'XYZ Enterprise',
                'email'                => 'contact2@xyze.com',
                'phone'                => '+8801900000002',
                'address'              => 'Chittagong, Bangladesh',
                'vat_no'               => 'VAT987654321',
                'payment_term'         => 30,
                'contact_person'       => 'Ms. Jane Smith',
                'contact_person_phone' => '+8801600000002',
                'contact_person_email' => 'jane@xyze.com',
                'country'              => 'Bangladesh',
                'opening_date'         => Carbon::now()->subMonth()->toDateString(),
                'opening_balance'      => 1000.00,
                'current_balance'      => 1000.00,
                'active_status'        => 1,
                'branch_office_id'     => null,
                'created_at'           => Carbon::now(),
                'updated_at'           => Carbon::now(),
            ],
        ]);
    }
}
