<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <title>WPS Salary Report</title>
        <style>
            body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
            .header { text-align: center; margin-bottom: 10px; }
            .meta { margin-bottom: 10px; font-size: 12px; }
            table { width: 100%; border-collapse: collapse; }
            th, td { border: 1px solid #444; padding: 4px 6px; vertical-align: top; }
            th { background: #f2f2f2; font-weight: bold; }
            .text-center { text-align: center; }
            .text-right { text-align: right; }
        </style>
    </head>
    <body>
        @php
            $monthNames = [
                1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June',
                7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
            ];

            $monthLabel = $monthNames[(int) $month] ?? (string) $month;

            $remarkForRow = function ($row): string {
                if (empty($row->akama_expire_date)) {
                    return 'Iqama Expired!';
                }
                $expire = strtotime((string) $row->akama_expire_date);
                if ($expire === false || $expire < strtotime(date('Y-m-d'))) {
                    return 'Iqama Expired!';
                }

                $iban = strtoupper(trim((string) ($row->iban ?? '')));
                if (!(str_starts_with($iban, 'SA') && strlen($iban) === 24)) {
                    return 'Invalid IBAN!';
                }

                $salary = (float) ($row->slh_total_salary ?? 0);
                if ($salary <= 0) {
                    return 'Invalid Salary!';
                }

                return 'OK';
            };
        @endphp

        <div class="header">
            <h3 style="margin:0;">WPS Salary Report</h3>
        </div>

        <div class="meta">
            <strong>Month:</strong> {{ $monthLabel }} &nbsp;&nbsp;
            <strong>Year:</strong> {{ $year }} &nbsp;&nbsp;
            <strong>Generated:</strong> {{ now()->format('d M Y h:i A') }}
        </div>

        <table>
            <thead>
                <tr>
                    <th class="text-center" style="width: 30px;">#</th>
                    <th style="width: 70px;">Emp ID</th>
                    <th>Employee</th>
                    <th style="width: 80px;">Iqama</th>
                    <th style="width: 90px;">IBAN</th>
                    <th style="width: 60px;">Bank</th>
                    <th style="width: 90px;">Account</th>
                    <th style="width: 90px;">Project</th>
                    <th class="text-right" style="width: 70px;">Basic</th>
                    <th class="text-right" style="width: 70px;">House</th>
                    <th class="text-right" style="width: 80px;">Salary</th>
                    <th style="width: 90px;">Remarks</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $i => $row)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td>{{ $row->employee_id }}</td>
                        <td>
                            <strong>{{ $row->employee_name }}</strong><br>
                            <small>{{ $row->designation }}</small>
                        </td>
                        <td>{{ $row->akama_no }}</td>
                        <td>{{ $row->iban }}</td>
                        <td>{{ $row->bank_code }}</td>
                        <td>{{ $row->account_number }}</td>
                        <td>{{ $row->project_name }}</td>
                        <td class="text-right">{{ $row->basic_amount }}</td>
                        <td class="text-right">{{ $row->house_rent }}</td>
                        <td class="text-right">{{ $row->slh_total_salary }}</td>
                        <td>{{ $remarkForRow($row) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </body>
</html>

