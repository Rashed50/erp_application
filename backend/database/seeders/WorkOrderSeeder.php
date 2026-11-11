<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WorkOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('work_orders')->insert([
            [
                'customer_id'       => 1, // নিশ্চিত করুন যে customers টেবিলে এই আইডি আছে
                'work_title'        => 'Software Development Project',
                'work_order_no'     => 'WO-2026-001',
                'issue_date'        => Carbon::now()->subDays(5),
                'total_amount'      => 50000.00,
                'retention_percent' => 5.00,
                'deliver_date'      => Carbon::now()->addDays(25),
                'status'            => 'Pending',
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'customer_id'       => 2, // দ্বিতীয় কাস্টমার আইডি (অথবা 1 ও দিতে পারেন)
                'work_title'        => 'Web Redesign & Maintenance',
                'work_order_no'     => 'WO-2026-002',
                'issue_date'        => Carbon::now()->subDays(2),
                'total_amount'      => 25000.50,
                'retention_percent' => 0.00,
                'deliver_date'      => Carbon::now()->addDays(15),
                'status'            => 'Pending',
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
        ]);
    }
}
