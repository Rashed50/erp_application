<?php

namespace Database\Seeders;

use App\Models\AccountType;
use App\Models\ChartOfAccount;
use Illuminate\Database\Seeder;

class ChartOfAccountSeeder extends Seeder
{
    /**
     * The predefined chart of accounts, ported from the payroll_software
     * Account module. Each group is followed by its transaction accounts.
     *
     * @var array<int, array{number: string, name: string, type: int, children: array<string, string>}>
     */
    protected array $groups = [
        [
            'number' => '1000', 'name' => 'Assets', 'type' => AccountType::ASSET,
            'children' => [
                '1010' => 'Cash in Hand',
                '1020' => 'Bank Accounts',
                '1030' => 'Accounts Receivable (Control)',
                '1040' => 'Inventory/Stock',
                '1050' => 'Advance to Vendors (Control)',
                '1060' => 'Prepaid Expenses',
                '1070' => 'Fixed Assets',
            ],
        ],
        [
            'number' => '2000', 'name' => 'Liabilities', 'type' => AccountType::LIABILITY,
            'children' => [
                '2010' => 'Accounts Payable (Control)',
                '2020' => 'Accrued Expenses',
                '2030' => 'Employee Payables (Salary Payable)',
                '2040' => 'Loans Payable',
                '2050' => 'Taxes Payable',
            ],
        ],
        [
            'number' => '3000', 'name' => 'Owner’s Equity', 'type' => AccountType::OWNER_EQUITY,
            'children' => [
                '3010' => 'Capital',
                '3020' => 'Retained Earnings',
                '3030' => 'Drawings',
            ],
        ],
        [
            'number' => '4000', 'name' => 'Revenue', 'type' => AccountType::REVENUE,
            'children' => [
                '4010' => 'Sales Revenue',
                '4020' => 'Service Income',
                '4030' => 'Other Income',
            ],
        ],
        [
            'number' => '5000', 'name' => 'Expenses', 'type' => AccountType::EXPENSE,
            'children' => [
                '5010' => 'Purchase',
                '5020' => 'Salaries & Wages',
                '5030' => 'Rent Expense',
                '5040' => 'Utilities Expense',
                '5050' => 'Office Expense',
                '5060' => 'Repairs & Maintenance',
                '5070' => 'Transportation/Logistics',
                '5080' => 'Miscellaneous Expenses',
                '5090' => 'Bank Charges',
            ],
        ],
    ];

    /**
     * Run the database seeds. Safe to re-run: accounts are matched by number.
     */
    public function run(): void
    {
        foreach ($this->groups as $group) {
            $parent = $this->seedAccount($group['number'], $group['name'], $group['type'], null, isTransaction: false);

            foreach ($group['children'] as $number => $name) {
                $this->seedAccount((string) $number, $name, $group['type'], $parent, isTransaction: true);
            }
        }
    }

    private function seedAccount(string $number, string $name, int $accountTypeId, ?ChartOfAccount $parent, bool $isTransaction): ChartOfAccount
    {
        return ChartOfAccount::query()->updateOrCreate(
            ['account_number' => $number],
            [
                'name' => $name,
                'account_type_id' => $accountTypeId,
                'parent_id' => $parent?->id,
                'sibling_level' => $parent ? $parent->sibling_level + 1 : 0,
                'opening_date' => today(),
                'is_transaction' => $isTransaction,
                'is_predefined' => true,
            ],
        );
    }
}
