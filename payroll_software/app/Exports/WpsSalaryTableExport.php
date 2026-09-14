<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class WpsSalaryTableExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    /** @var \Illuminate\Support\Collection<int, object> */
    private Collection $rows;

    private int $counter = 1;

    /**
     * @param \Illuminate\Support\Collection<int, object> $rows
     */
    public function __construct(Collection $rows)
    {
        $this->rows = $rows;
    }

    public function collection(): Collection
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'S.N',
            'Employee ID',
            'Employee Name',
            'Iqama No',
            'Iqama Expire Date',
            'IBAN',
            'Account No',
            'Bank Code',
            'Designation',
            'Project',
            'Basic',
            'House Rent',
            'Total Salary',
            'Month',
            'Year',
            'Remarks',
        ];
    }

    public function map($row): array
    {
        $remark = $this->remarkForRow($row);

        return [
            $this->counter++,
            $row->employee_id ?? '',
            $row->employee_name ?? '',
            $row->akama_no ?? '',
            $row->akama_expire_date ?? '',
            $row->iban ?? '',
            $row->account_number ?? '',
            $row->bank_code ?? '',
            $row->designation ?? '',
            $row->project_name ?? '',
            $row->basic_amount ?? '',
            $row->house_rent ?? '',
            $row->slh_total_salary ?? '',
            $row->slh_month ?? '',
            $row->slh_year ?? '',
            $remark,
        ];
    }

    private function remarkForRow($row): string
    {
        // Iqama expired (same logic as the Vue UI)
        if (empty($row->akama_expire_date)) {
            return 'Iqama Expired!';
        }

        try {
            $expire = strtotime((string) $row->akama_expire_date);
            if ($expire === false || $expire < strtotime(date('Y-m-d'))) {
                return 'Iqama Expired!';
            }
        } catch (\Throwable $e) {
            return 'Iqama Expired!';
        }

        // Invalid IBAN (must start with SA and length 24)
        $iban = strtoupper(trim((string) ($row->iban ?? '')));
        if (!(str_starts_with($iban, 'SA') && strlen($iban) === 24)) {
            return 'Invalid IBAN!';
        }

        // Invalid salary
        $salary = (float) ($row->slh_total_salary ?? 0);
        if ($salary <= 0) {
            return 'Invalid Salary!';
        }

        return 'OK';
    }
}

