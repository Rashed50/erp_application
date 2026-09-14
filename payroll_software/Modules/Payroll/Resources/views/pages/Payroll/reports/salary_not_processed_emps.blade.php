<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Subcon-Details</title>
    <style>
        /* 1. GLOBAL FONT SETTING */
        * {
            /* This ensures every single element uses the same font */
            font-family: 'DejaVu Sans', sans-serif !important;
        }

        body {
            background-color: #f0f0f0;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            font-size: 12px;
            color: #333;
        }

        /* A4 Wrapper */
        .page-container {
            background-color: white;
            width: 297mm;         /* Swapped: A4 Length */
            min-height: 210mm;    /* Swapped: A4 Height */
            padding-left: 12mm;
            padding-right: 7mm;
            padding-top: 5mm;
            padding-bottom: 15mm;
            margin: 0 auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
            box-sizing: border-box;
            position: relative;
            overflow: hidden;
        }

        /* WATERMARK DESIGN */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 50pt;
            color: rgba(0, 0, 0, 0.05); /* Very light grey */
            font-weight: normal;
            z-index: 0;
            pointer-events: none;
            white-space: nowrap;
            text-transform: uppercase;
        }

        /* BOTTOM DISCLAIMER */
        .disclaimer {
            position: absolute;
            bottom: 5mm;
            left: 12mm;
            right: 7mm;
            font-size: 9px;
            color: #777;
            border-top: 0.5px solid lightgrey;
            padding-top: 5px;
            text-align: center;
        }

        @media print {
            body { background-color: white; padding: 0; display: block; }
            @page { size: A4 landscape; margin: 0; }
            .page-container {
                width: 100%;
                box-shadow: none;
                margin: 0;
                padding-left: 12mm;
                padding-right: 7mm;
                padding-top: 5mm;
                padding-bottom: 7mm;
            }
            .no-print { display: none !important; }

            #employeeinfo th {
                background-color: #FFD700 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .bg-success { background-color: #d4edda !important; -webkit-print-color-adjust: exact; }
            .bg-danger { background-color: #f8d7da !important; -webkit-print-color-adjust: exact; }

            .watermark { color: rgba(0, 0, 0, 0.05) !important; -webkit-print-color-adjust: exact; }
        }

        /* 2. TABLE & CONTENT STYLES */
        header {
            width: 100%;
            border-bottom: 1px solid lightgrey;
            padding-bottom: 10px;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }

        #employeeinfo {
            width: 100%;
            border-collapse: collapse;
            position: relative;
            z-index: 1;
        }

        #employeeinfo th, #employeeinfo td {
            border: 0.5px solid lightgrey;
            padding: 8px;
            font-size: 12px;
        }

        #employeeinfo th {
            background-color: #FFD700;
            font-weight: bold;
            text-align: center;
        }

        .td__center { text-align: center; }

        .bg-success { background-color: #d4edda; color: #155724; }
        .bg-danger { background-color: #f8d7da; color: #721c24; }

        .signature-table {
            width: 100%;
            margin-top: 60px;
            position: relative;
            z-index: 1;
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
    </style>
</head>
<body>

    <div class="page-container">
        <div class="watermark">ASLOOB BEDAA CO.</div>

        <header>
            <table style="width: 100%; border: none;">
                <tr style="border: none;">
                    <td style="width: 25%; text-align: left; border: none;">
                        <img src="{{ public_path('contents/admin/assets/images/logo_new.png') }}" alt="Logo" style="height: 60px;">
                    </td>
                    <td style="width: 50%; text-align: center; border: none;">
                        <div style="font-size: 18px; font-weight: bold;">{{ $company->comp_name_en }}</div>
                        <div style="font-size: 10px;">{{ $company->comp_address }}</div>
                    </td>
                    <td style="width: 25%; text-align: right; border: none; font-size: 10px;">
                        Date: {{ date('d M Y h:i A') }} <br><br>
                        <button onclick="window.print()" class="print__button no-print">PRINT</button>
                    </td>
                </tr>
            </table>
        </header>

        <main>
            <h4 style="text-align:center; font-weight: bold;">Employees whose salary has not been processed </h4>

            <table id="employeeinfo">
                <thead>
                    <tr>
                        <th>S.N</th>
                        <th>Employee Name</th>
                        <th>Employee ID</th>
                        <th>iqama No</th>
                        <th>Designation</th>
                        <th>Project Name</th>
                        <th>Basic Hours</th>
                        <th>OT</th>
                        <th>Total Hours</th>
                        <th>Day</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach($employees as $ar)
                        @php
                        @endphp
                        <tr>
                            <td class="td__center">{{ $loop->iteration }}</td>
                            <td>{{ $ar->employee_name }}</td>
                            <td class="td__center">{{ $ar->employee_id }}</td>
                            <td class="td__center"> {{ $ar->akama_no }}</td>
                            <td class="td__center">{{ $ar->designation }}</td>
                            <td class="td__center">{{ $ar->proj_name }}</td>
                            <td class="td__center">{{ $ar->total_hours }}</td>
                            <td class="td__center">{{ $ar->overtime }}</td>
                            <td class="td__center">{{ $ar->total_hours +  $ar->overtime }}</td>
                            <td class="td__center">{{ $ar->total_work_day }} </td>
                            <td class="td__center {{ $ar->job_status == 1 ? 'bg-success' : 'bg-danger' }}">
                                {{ $ar->job_status == 1 ? 'Active' : 'Inactive' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>

            <table class="signature-table" style="border: none;">
                <tr style="border: none;">
                    <td style="width: 33%; text-align: left; border: none;">
                        <p><b>{{ $login_name }}</b><br>Prepared By</p>
                    </td>
                    <td style="width: 33%; text-align: center; border: none;">
                        <p> <br>Verified By</p>
                    </td>
                    <td style="width: 33%; text-align: right; border: none;">
                        <p><b>Authorized Signature</b></p>
                    </td>
                </tr>
            </table>
        </main>

        <div class="disclaimer">
            This is a computer-generated report. No signature is required for authentication of digital records. Confidential - {{ $company->comp_name_en }}
        </div>
    </div>

</body>
</html>
