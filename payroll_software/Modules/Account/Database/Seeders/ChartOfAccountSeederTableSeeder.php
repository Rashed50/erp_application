<?php

namespace Modules\Account\Database\Seeders;

use App\Models\AccountsModule\ChartOfAccounts;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class ChartOfAccountSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $chartOfAccounts = [
              [
                'chart_of_acct_name' => 'Assets',
                'chart_of_acct_number' => '1000',
                'type' => 'asset',
                'parent_id' => null
            ],
             [
                'chart_of_acct_name' => 'Cash in Hand',
                'chart_of_acct_number' => '1010',
                'type' => 'asset',
                'parent_id' => 1
            ],
             [
                'chart_of_acct_name' => 'Bank Accounts',
                'chart_of_acct_number' => '1020',
                'type' => 'asset',
                'parent_id' => 1
            ],
             [
                'chart_of_acct_name' => 'Accounts Receivable(Control)',
                'chart_of_acct_number' => '1030',
                'type' => 'asset',
                'parent_id' => 1
            ],
            [
                'chart_of_acct_name' => 'Inventory/Stock',
                'chart_of_acct_number' => '1040',
                'type' => 'asset',
                'parent_id' => 1
            ],
            [
                'chart_of_acct_name' => 'Advance to Vendors(Controll)',
                'chart_of_acct_number' => '1050',
                'type' => 'asset',
                'parent_id' => 1
            ],
             [
                'chart_of_acct_name' => 'Prepaid Expenses',
                'chart_of_acct_number' => '1060',
                'type' => 'asset',
                'parent_id' => 1
            ],
            [
                'chart_of_acct_name' => 'Fixed Assets',
                'chart_of_acct_number' => '1070',
                'type' => 'asset',
                'parent_id' => 1
            ],
            //2 Liability
             [
                'chart_of_acct_name' => 'Liabilities',
                'chart_of_acct_number' => '2000',
                'type' => 'liability',
                'parent_id' => null
            ],
            [
                'chart_of_acct_name' => 'Accounts Payable (Control)',
                'chart_of_acct_number' => '2010',
                'type' => 'liability',
                'parent_id' => 2
            ],
             [
                'chart_of_acct_name' => 'Accrued Expenses',
                'chart_of_acct_number' => '2020',
                'type' => 'liability',
                'parent_id' => 2
            ],
             [
                'chart_of_acct_name' => 'Employee Payables(Salary Payable)',
                'chart_of_acct_number' => '2030',
                'type' => 'liability',
                'parent_id' => 2
            ],
             [
                'chart_of_acct_name' => 'Loans Payable',
                'chart_of_acct_number' => '2040',
                'type' => 'liability',
                'parent_id' => 2
            ],
             [
                'chart_of_acct_name' => 'Taxes Payable',
                'chart_of_acct_number' => '2050',
                'type' => 'liability',
                'parent_id' => 2
            ],
            //3 Owner Equity
             [
                'chart_of_acct_name' => 'Owner’s Equity',
                'chart_of_acct_number' => '3000',
                'type' => 'equity',
                'parent_id' => null
            ],
             [
                'chart_of_acct_name' => 'Capital', // Owner's Capital/Equity	
                'chart_of_acct_number' => '3010',
                'type' => 'equity',
                'parent_id' => 3
            ],
             [
                'chart_of_acct_name' => 'Retained Earnings',
                'chart_of_acct_number' => '3020',
                'type' => 'equity',
                'parent_id' => 3
            ],
             [
                'chart_of_acct_name' => 'Drawings',
                'chart_of_acct_number' => '3030',
                'type' => 'equity',
                'parent_id' => 3
            ],
            //4 Revenue
             [
                'chart_of_acct_name' => 'Revenue',
                'chart_of_acct_number' => '4000',
                'type' => 'revenue',
                'parent_id' => null
            ],
            [
                'chart_of_acct_name' => 'Sales Revenue',
                'chart_of_acct_number' => '4010',
                'type' => 'revenue',
                'parent_id' => 4
            ],
            [
                'chart_of_acct_name' => 'Service Income',
                'chart_of_acct_number' => '4020',
                'type' => 'revenue',
                'parent_id' => 4
            ],
            [
                'chart_of_acct_name' => 'Other Income',
                'chart_of_acct_number' => '4030',
                'type' => 'revenue',
                'parent_id' => 4
            ],
            //5 Expenses
            [
                'chart_of_acct_name' => 'Expenses',
                'chart_of_acct_number' => '5000',
                'type' => 'expense',
                'parent_id' => null
            ],
             [
                'chart_of_acct_name' => 'Purchase',
                'chart_of_acct_number' => '5010',
                'type' => 'expense',
                'parent_id' => 5
            ],
             [
                'chart_of_acct_name' => 'Salaries & Wages',
                'chart_of_acct_number' => '5020',
                'type' => 'expense',
                'parent_id' => 5
            ],
             [
                'chart_of_acct_name' => 'Rent Expense',
                'chart_of_acct_number' => '5030',
                'type' => 'expense',
                'parent_id' => 5
            ],
             [
                'chart_of_acct_name' => 'Utilities Expense',
                'chart_of_acct_number' => '5040',
                'type' => 'expense',
                'parent_id' => 5
            ],
             [
                'chart_of_acct_name' => 'Office Expense',
                'chart_of_acct_number' => '5050',
                'type' => 'expense',
                'parent_id' => 5
            ],
             [
                'chart_of_acct_name' => 'Repairs & Maintenance',
                'chart_of_acct_number' => '5060',
                'type' => 'expense',
                'parent_id' => 5
            ],
             [
                'chart_of_acct_name' => 'Transportation/Logistics',
                'chart_of_acct_number' => '5070',
                'type' => 'expense',
                'parent_id' => 5
            ],
             [
                'chart_of_acct_name' => 'Miscellaneous Expenses',
                'chart_of_acct_number' => '5080',
                'type' => 'expense',
                'parent_id' => 5
            ]
        ];


        function get_account_type($type)
        {
            switch ($type) {
                case 'asset':
                    return 1;
                    break;
                case 'liability':
                    return 2;
                    break;
                case 'equity':
                    return 3;
                    break;
                case 'revenue':
                    return 4;
                    break;
                case 'expense':
                    return 5;
                    break;
                default:
                    return 0;
                    break;
            }
        }


        $parent_id_mapping = [];

        $i = 1;
        foreach ($chartOfAccounts as $chartOfAccount) {
            $c = [
                'chart_of_acct_name' => $chartOfAccount['chart_of_acct_name'],
                'chart_of_acct_number' => intval($chartOfAccount['chart_of_acct_number']),
                'acct_balance' => 0,
                'opening_date' => now(),
                'acct_type_id' => get_account_type($chartOfAccount['type']),
                'active_status' => 1,
                'is_transaction' => 0,
                'is_predefined' => 1,
                'is_closed' => 0,
                'created_by_id' => 1,
                'updated_by_id' => 1,
            ];

            if ($chartOfAccount['parent_id'] != null) {
                  $c['parent_id']= $parent_id_mapping[$chartOfAccount['parent_id']];
            }

             $parent_idd = ChartOfAccounts::create($c)->chart_of_acct_id;

            if ($chartOfAccount['parent_id'] == null) {
                    $parent_id_mapping[$i++] = $parent_idd;
            }
        }
    }
}
