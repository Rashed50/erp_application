<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Subcontractor Summary Report.</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        /* The key CSS for the header */
        header {
            position: fixed;
            top: -50px;
            /* Adjust as needed */
            left: 0;
            right: 0;
            height: 50px;
            text-align: center;
            /* line-height: 35px; */
            font-size: 14px;
        }

        /* The key CSS for the footer */
        footer {
            position: fixed;
            bottom: -50px;
            /* Adjust as needed */
            left: 0;
            right: 0;
            height: 20px;
            text-align: center;
            font-size: 12px;
            color: #555;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            padding-bottom: 10px;

        }

        /* Adjust content margins to prevent overlap */
        .content {
            padding: 20px;
            margin-top: 30px;
            /* Slightly more than header height */
            margin-bottom: 60px;
            /* Slightly more than footer height */
        }

        h1,
        h2,
        p {
            margin: 0 0 10px 0;
        }



        .header-table {
            width: 100%;
            margin-bottom: 12px;
            border-collapse: collapse;
            padding: 18px;
            border-radius: 3px;
            margin: 0;
        }

        .header-table td {
            vertical-align: middle;
        }

        .company-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 4px;
            color: #222;
        }

        .subtitle {
            font-size: 11px;
            color: #666;
        }



        .info-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
            margin-top: 20px;
            margin-bottom: 10px;
        }

        .info-table td {
            vertical-align: top;
            padding: 5px;
            border: none;
        }


        .content-hr {
            border: 0;
            border-top: 2px dotted #bbb;
            margin: 20px 0;
        }

        .watermark {
            position: fixed;
            top: 40%;
            left: 15%;
            opacity: 0.05;
            font-size: 100px;
            transform: rotate(-30deg);
            z-index: -1;
        }

        .top-hr {
            border: 0;
            border-top: 3px solid lightblue;
            margin: 0;
        }


        .signatures {
            display: flex;
            justify-content: space-between;
            width: 100%;
            margin-top: 50px;
        }

        .signature-block {
            width: 23%;
            /* Adjusted for four columns */
            text-align: center;
        }

        .signature-line {
            border-bottom: 1px solid #000;
            height: 20px;
            margin-bottom: 5px;
        }


        /* Table Styles */
        .table-container {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .table-title {
            text-align: center;
            font-weight: bold;
            font-size: 12px;
            background-color: #d9d9d9;
            color: #000;
            padding: 6px;
            margin-bottom: 0;
            border: 1px solid #000;
        }

        .table-container table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }

        .table-container th {
            background-color: #f0f0f0;
            color: #000;
            font-weight: bold;
            padding: 6px 4px;
            text-align: center;
            border: 1px solid #000;
            font-size: 9px;
        }

        .table-container td {
            padding: 5px 4px;
            border: 1px solid #000;
            text-align: center;
            font-size: 9px;
        }

        .total-row {
            background-color: #f0f0f0;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <!-- Watermark -->
    <div class="watermark">ASLOOB BEDAA CO.</div>
    <header>

        <!-- Header -->
        <table class="header-table">
            <tr>
                <td style="width: 65%;">
                    {{-- <img src="{{ public_path('contents/admin/assets/images/logo_new.png') }}"
                        style="width: 130px; height: auto;"> --}}
                    <img src="{{ asset($company->com_logo) }}"  alt="" width="184px" height="80px">
                </td>
                <td style="width: 35%; text-align: end;">
                    <div class="company-title">ASLOOB BEDAA CO.</div>
                    <div class="subtitle">{{ $company->comp_address }}</div>
                    {{-- <div class="subtitle"> {{ $company->comp_phone1 }} </div>
                    <div class="subtitle">{{ $company->comp_email1 }} </div> --}}
                </td>
            </tr>
        </table>
        <hr class="top-hr">
    </header>

    <div class="content">
        <table class="info-table">
            <tr>
                <!-- SubContractor -->
                <td style="width: 70%;">
                    <strong>SubContractor</strong><br>
                    <span style="margin-top: 5px;"> {{ $subcontract_info->subcon_name }} </span><br>
                    <span> Iqama No: {{ $subcontract_info->iqama_no }}</span><br>
                    <span> Passport No: {{ $subcontract_info->passfort_no }}</span><br>
                    <span> Mobile No: {{ $subcontract_info->mobile_no }}</span><br>
                </td>
                <!-- Reference -->
            </tr>
        </table>

        <h4 style="text-align: center;"><u>Outstanding Summary</u></h4>
        <table class="info-table">
            <tr>
                <!-- Total Invoice Amount -->
                <td style="width: 33%; text-align:center;background:lightgray;">
                    <strong>Total Invoice Amount</strong><br>
                    <span> {{ $total_invoice_amount }} </span><br>
                </td>

                <!-- Total Paid Amount -->
                <td style="width: 33%;  text-align:center;background:rgb(114, 186, 228);">
                    <strong>Pay Amount</strong><br>
                    <span > {{ $total_paid_amount }} </span><br>
                </td>

                <td style="width: 33%;  text-align:center;background:rgb(67, 212, 152);">
                    <strong>Outstanding </strong><br>
                    <span > {{ $total_invoice_amount - $total_paid_amount }} </span><br>
                </td>
            </tr>
        </table>

        <hr class="content-hr">

       <!-- Table 2: Date-wise Payment Summary -->
        <div class="table-container">
            <div class="table-title">Date-wise Payment Summary</div>
            <table>
                <thead>
                    <tr>
                        <th width="5%">S.N</th>
                        <th width="10%">Month,Year</th>
                        <th width="10%">Invoice Amount</th>
                        <th width="10%">Pay Amount</th>
                        <th width="15%">Payment Type</th>
                        <th width="15%">Payment Method</th>
                        <th width="15%">Remarks</th>
                        <th width="15%">Attach File</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($total_payment_transaction as $index => $item)
                        <tr>
                            <td>{{ $index }}</td>
                            <td>{{ date("F", mktime(0, 0, 0, $item->month, 10)) }}, {{ $item->year }}</td>
                            <td>{{ $item->invoice_amount }}</td>
                            <td>{{ $item->pay_amount }}</td>
                            <td class="text-right">{{ $item->payment_type }}</td>
                            <td class="text-right">{{ $item->payment_method == 1 ? 'Cash' : 'Bank' }}</td>
                            <td>{{ $item->payment_remarks }}</td>
                            <td>

                                @if ($item->payment_file)
                                    @php
                                        $ext = pathinfo($item->payment_file, PATHINFO_EXTENSION);
                                    @endphp

                                    @if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                        <img src="{{ asset($item->payment_file) }}" alt="Preview"
                                            style="width:80px; height:auto; border-radius:5px;">
                                    @elseif(in_array(strtolower($ext), ['pdf']))
                                        <a href="{{ asset($item->payment_file) }}" target="_blank">View PDF</a>
                                    @else
                                        <a href="{{ asset($item->payment_file) }}" target="_blank">Download File</a>
                                    @endif
                                @else
                                    <span class="text-muted"></span>
                                @endif

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>



    </div>


    <footer>
        <p> System Generated Statement Authorized by Accounts Department. &copy; {{ date('Y-m-d') }}</p>
        {{-- <p>Contact us at {{ $company->comp_address }}, {{ $company->comp_phone1 }},{{ $company->comp_email1 }} </p> --}}
    </footer>


</body>

</html>
