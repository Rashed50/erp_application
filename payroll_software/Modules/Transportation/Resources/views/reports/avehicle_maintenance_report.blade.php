<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Veh. Maintenance Report</title>
    <style>
        /* 1. GLOBAL SETTINGS & A4 SIMULATION */
        * {
            font-family: 'DejaVu Sans', sans-serif !important;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #f0f0f0; /* Gray background for screen */
            padding: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            font-size: 12px;
            color: #333;
        }

        /* A4 Landscape Wrapper */
        .page-container {
            background-color: white;
            width: 297mm;      /* A4 landscape width */
            min-height: 210mm; /* A4 landscape height */
            padding: 10mm 7mm 15mm 12mm;
            margin: 0 auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
            position: relative; /* Required for Watermark positioning */
            overflow: hidden;   /* Keeps watermark inside bounds */
        }

        /* 2. WATERMARK STYLE */
        .page-container::before {
            content: "{{ $company->comp_name_en }}";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 80px;
            color: rgba(200, 200, 200, 0.15); /* Transparency */
            font-weight: bold;
            z-index: 0;
            pointer-events: none;
            white-space: nowrap;
        }

        @media print {
            body { background-color: white; padding: 0; display: block; }
            @page { size: A4 landscape; margin: 5mm; }
            .page-container {
                width: 100%;
                box-shadow: none;
                margin: 0;
                padding: 10mm;
                min-height: auto;
            }
            .no-print { display: none !important; }

            /* Ensure colors and watermark show in print */
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }

            .print-footer-fixed {
                position: fixed;
                bottom: 0;
                left: 0;
                width: 100%;
                display: block !important;
            }
        }

        /* 3. TABLE & CONTENT STYLES */
        header {
            width: 100%;
            border-bottom: 1px solid lightgrey;
            padding-bottom: 10px;
            margin-bottom: 15px;
            position: relative;
            z-index: 1;
        }

        main { width: 100%; position: relative; z-index: 1; }

        #maintenance-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        #maintenance-table th, #maintenance-table td {
            border: 0.5px solid lightgrey;
            padding: 6px;
            font-size: 11px; /* Slightly smaller to ensure fit */
        }

        /* Remove vertical borders from the main maintenance table as requested */
        #maintenance-table td, #maintenance-table th {
            border-left: none;
            border-right: none;
        }

        #maintenance-table th {
            background-color: #FFD700 !important;
            font-weight: bold;
            text-align: center;
        }

        .td__center { text-align: center; }
        .td__name { text-align: left; }

        .vehicle-details-info {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }

        .vehicle-details-info td {
            padding: 4px 8px;
            border: none;
            font-size: 12px;
            border-bottom: 1px solid #f9f9f9;
        }

        .vehicle-details-info td.label { font-weight: bold; width: 15%; }

        /* 4. FOOTER STYLES */
        .report-footer {
            margin-top: 10px;
            text-align: center;
            font-size: 10px;
            color: #777;
            border-top: 1px solid #eee;
            padding-top: 5px;
        }

        .print-footer-fixed {
            display: none; /* Hidden on screen */
            font-size: 9px;
            color: #999;
            padding: 2px 10mm;
        }

        .print__button {
            background: #000;
            color: #fff;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="page-container">
        <header>
            <table style="width: 100%; border: none;">
                <tr style="border: none;">
                    <td style="width: 25%; text-align: left; border: none;">
                        <img src="{{ asset($company->com_logo) }}" alt="Logo" width="200px" height="60px">
                    </td>
                    <td style="width: 50%; text-align: center; border: none;">
                        <div style="font-size: 18px; font-weight: bold;">{{ $company->comp_name_en ?? 'Company Name' }}</div>
                        <div style="font-size: 12px;">{{ $company->comp_address ?? 'Address' }}</div>
                    </td>
                    <td style="width: 25%; text-align: right; border: none; font-size: 11px;">
                        Date: {{ date('Y-m-d') }} <br><br>
                        <button onclick="window.print()" class="print__button no-print">PRINT REPORT</button>
                    </td>
                </tr>
            </table>
        </header>

        <main>
            <h3 style="text-align: center; margin-bottom: 15px;">Vehicle Maintenance Details Report</h3>

            @php
                $vehicle = is_object($records) ? $records->vehicle : (is_array($records) && isset($records['vehicle']) ? $records['vehicle'] : null);
                $total_amount = 0;
            @endphp

            @if($vehicle)
            <table class="vehicle-details-info">
                <tr>
                    <td class="label">Vehicle Name:</td><td>{{ $vehicle->veh_name ?? 'N/A' }}</td>
                    <td class="label">Plate No:</td><td>{{ $vehicle->veh_plate_number ?? 'N/A' }}</td>
                    <td class="label">Brand:</td><td>{{ $vehicle->veh_brand_name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">Reg. No:</td><td>{{ $vehicle->veh_licence_no ?? 'N/A' }}</td>
                    <td class="label">Model:</td><td>{{ $vehicle->veh_model_number ?? 'N/A' }}</td>
                    <td class="label">Driver:</td><td>{{ $vehicle->dri_name ?? 'N/A' }}</td>
                </tr>
            </table>
            @endif

            <table id="maintenance-table">
                <thead>
                    <tr>
                        <th>S.N</th>
                        <th>Veh.No.</th>
                        <th>M.Type</th>
                        <th>Description</th>
                        <th>M.Date</th>
                        <th>Mileage</th>
                        <th>Qty</th>
                        <th>Amount</th>
                        <th>Inv. No.</th>
                        <th>Drive By</th>
                        <th>Location</th>
                        <th>Remarks</th>
                        <th>Invoice</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $maintenance_list = is_object($records) && isset($records->maintenance_records) ? $records->maintenance_records : (is_array($records) && isset($records['maintenance_records']) ? $records['maintenance_records'] : []);
                    @endphp
                    @foreach($maintenance_list as $ar)
                        @php $total_amount += $ar->total_amount; @endphp
                        <tr>
                            <td class="td__center">{{ $loop->iteration }}</td>
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
                            <td class="td__center">{{ $ar->Remarks ?? '-' }}</td>
                            <td class="td__center">
                                @if($ar->invoice_file)
                                    <a href="{{ config('app.aws_s3_bucket_url').$ar->invoice_file }}" target="_blank"  title="Download Invoice"
                                       style="color: #0066cc; text-decoration: none; font-size: 11px;">
                                        📄
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot style="background-color: #f9f9f9;">
                    <tr style="border-top: 2px solid black;">
                        <td colspan="7" style="font-weight:bold; text-align:right; border:none;">TOTAL:</td>
                        <td class="td__center" style="font-weight:bold; border:none;">{{ number_format($total_amount, 2) }}</td>
                        <td colspan="4" style="border:none;"></td>
                    </tr>
                </tfoot>
            </table>

            <table style="width: 100%; margin-top: 50px; border: none;">
                <tr style="border: none;">
                     <td style="width: 33.33%; text-align: left; border: none;">
                        <p style="margin-bottom: 5px;"><b>----------------------</b></p>
                        <p><b>{{ $login_name ?? 'User' }}</b><br>Prepared By</p>
                    </td>
                    <td style="width: 33.33%; text-align: left; border: none;">
                        <p style="margin-bottom: 5px;"><b>----------------------</b></p>
                        <p><b>{{ $login_name ?? 'User' }}</b><br>Verified By</p>
                    </td>
                    <td style="width: 33.33%; text-align: right; border: none;">
                        <p style="margin-bottom: 5px;"><b>-----------------------------</b></p>
                        <p><b>Authorized By</b></p>
                    </td>
                </tr>
            </table>

            {{-- <div class="report-footer">
                Online System Generated Statement Authorized by Accounts & HR Department
            </div> --}}
        </main>
    </div>

    <!-- This footer only appears at the bottom of the printed page -->
    <div class="print-footer-fixed">
        <table style="width: 100%; border-top: 0.5px solid #eee;">
            <tr>
                <td style="padding: 2px 0;">Generated: {{ date('Y-m-d H:i') }}</td>
                <td style="text-align: right; padding: 2px 0;">Online System Generated Statement Authorized by Accounts & HR Department</td>
            </tr>
        </table>
    </div>

</body>
</html>
