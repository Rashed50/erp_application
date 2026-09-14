<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Vehicle Maintenance Report - Projects View</title>
    <style>
        /* 1. GLOBAL FONT SETTING */
        * {
            font-family: 'DejaVu Sans', sans-serif !important;
        }

        body {
            background-color: #f0f0f0;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            font-size: 11px;
            color: #333;
        }

        /* A4 Landscape Wrapper */
        .page-container {
            background-color: white;
            width: 297mm;
            min-height: 210mm;
            padding-left: 12mm;
            padding-right: 7mm;
            padding-top: 10mm;
            padding-bottom: 7mm;
            margin: 0 auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
            box-sizing: border-box;
            position: relative;
        }

        @media print {
            body { background-color: white; padding: 0; display: block; }
            @page { size: A4 landscape; margin: 5mm; }
            .page-container { width: 100%; box-shadow: none; margin: 0; padding: 15mm; }
            .no-print { display: none !important; }
            table th {
                background-color: #FFD700 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        header {
            width: 100%;
            border-bottom: 1px solid lightgrey;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        #maintenance-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        #maintenance-table th, #maintenance-table td {
            border: 0.5px solid lightgrey;
            padding: 6px;
            font-size: 9px;
        }

        #maintenance-table th {
            background-color: #FFD700;
            font-weight: bold;
            text-align: center;
        }

        .td__center { text-align: center; }
        .td__name { text-align: left; }

        .signature-table {
            width: 100%;
            margin-top: 40px;
        }

        .print__button {
            background: #000;
            color: #fff;
            border: none;
            padding: 8px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }

        .filter-info {
            width: 100%;
            margin-top: 10px;
            margin-bottom: 10px;
            font-size: 10px;
            padding: 8px;
            background-color: #f9f9f9;
            border: 0.5px solid lightgrey;
        }

        main {
            width: 100%;
        }

        main h3 {
            text-align: center;
            font-weight: bold;
            margin: 10px 0;
            font-size: 14px;
        }
    </style>
</head>
<body>

    <div class="page-container">
        <header>
            <table style="width: 100%; border: none;">
                <tr style="border: none;">
                    <td style="width: 25%; text-align: left; border: none;">
                        <img src="{{ asset($company->com_logo) }}"  alt="Logo not found" width="200px" height="60px">
                    </td>
                    <td style="width: 50%; text-align: center; border: none;">
                        <div style="font-size: 16px; font-weight: bold;">{{ $company->comp_name_en ?? 'Company Name' }}</div>
                        <div style="font-size: 10px;">{{ $company->comp_address ?? 'Address' }}</div>
                    </td>
                    <td style="width: 25%; text-align: right; border: none; font-size: 10px;">
                        Date: {{ date('Y-m-d') }} <br><br>
                        <button onclick="window.print()" class="print__button no-print">PRINT</button>
                    </td>
                </tr>
            </table>
        </header>

        <main>
            <h3>Vehicle Maintenance Details Report (All Vehicles)</h3>

            @php
                $total_amount = 0;
            @endphp

            <!-- Filter Information Section -->
            <div class="filter-info">
                <strong>Report Filters:</strong>
                @if($from_date)
                    From: {{ $from_date }}
                @endif
                @if($to_date)
                    | To: {{ $to_date }}
                @endif
                @if(!empty($selected_project_names))
                    | <strong>Project(s):</strong> {{ $selected_project_names }}
                @endif
                | <strong>Generated:</strong> {{ date('Y-m-d H:i:s') }}
            </div>

            <!-- Maintenance Records Table with All Vehicles -->
            <table id="maintenance-table">
                <thead>
                    <tr>
                        <th>S.N</th>
                        <th>Vehicle<br>Name</th>
                        <th>Vehicle<br>Plate</th>
                        <th>Type</th>
                        <th>Maintenance<br>Name</th>
                        <th>Date</th>
                        <th>Mileage</th>
                        <th>Qty</th>
                        <th>Total<br>Amount</th>
                        <th>Invoice No.</th>
                        <th>Driver</th>
                        <th>Project</th>
                        <th>Invoice<br>File</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($maintenance_records as $ar)
                        @php
                            $total_amount += $ar->total_amount;
                        @endphp
                        <tr>
                            <td class="td__center">{{ $loop->iteration }}</td>
                            <td class="td__name">{{ $ar->veh_name ?? 'N/A' }}</td>
                            <td class="td__center">{{ $ar->veh_plate_number ?? 'N/A' }}</td>
                            <td class="td__center">{{ $ar->service_type ?? 'N/A' }}</td>
                            <td class="td__name">{{ $ar->service_name ?? 'N/A' }}</td>
                            <td class="td__center">{{ $ar->start_date ?? 'N/A' }}</td>
                            <td class="td__center">{{ $ar->current_mileage ?? 'N/A' }}</td>
                            <td class="td__center">{{ $ar->qty ?? 0 }}</td>
                            <td class="td__center">{{ number_format($ar->total_amount ?? 0, 2) }}</td>
                            <td class="td__center">{{ $ar->invoice_no ?? 'N/A' }}</td>
                            <td class="td__center">{{ $ar->dri_name ?? 'N/A' }}</td>
                            <td class="td__center">{{ $ar->proj_name ?? 'N/A' }}</td>
                            <td class="td__center">
                                @if($ar->invoice_file)
                                    <a href="{{ asset('storage/' . $ar->invoice_file) }}"
                                       target="_blank"
                                       title="Download Invoice"
                                       style="color: #0066cc; text-decoration: none; font-size: 8px;">
                                        📄 DL
                                    </a>
                                @else
                                    <span style="color: #999; font-size: 8px;">N/A</span>
                                @endif
                            </td>
                            <td class="td__center">{{ $ar->Remarks ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="14" style="text-align:center; padding: 12px; color: #777;">
                                No maintenance records found for the selected project(s).
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot style="background-color: #f9f9f9;">
                    <tr>
                        <td colspan="8" style="font-weight:bold; text-align:right;">TOTAL:</td>
                        <td class="td__center" style="font-weight:bold;">{{ number_format($total_amount, 2) }}</td>
                        <td colspan="5"></td>
                    </tr>
                </tfoot>
            </table>

            <!-- Signature Section -->
            <table class="signature-table" style="border: none;">
                <tr style="border: none;">
                    <td style="width: 50%; text-align: left; border: none;">
                        <p style="margin-bottom: 40px;"><b>----------------------</b></p>
                        <p><b>{{ $login_name ?? 'User' }}</b><br>Prepared By</p>
                    </td>
                    <td style="width: 50%; text-align: right; border: none;">
                        <p style="margin-bottom: 40px;"><b>-----------------------------</b></p>
                        <p><b>Authorized Signature</b></p>
                    </td>
                </tr>
            </table>
        </main>
    </div>

</body>
</html>
