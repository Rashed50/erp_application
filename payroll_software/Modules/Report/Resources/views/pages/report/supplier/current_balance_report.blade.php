<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Supplier Report</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 100px 40px 60px 40px;

            @bottom-left {
                content: "Generated on: {{ $current_datetime }}";
                font-size: 8pt;
                color: #666;
            }

            @bottom-center {
                content: "System Generated";
                font-size: 8pt;
                color: #666;
            }

            @bottom-right {
                content: "Page " counter(page) " of " counter(pages);
                font-size: 8pt;
                color: #666;
            }
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }

        /* Watermark */
        .watermark {
            position: fixed;
            top: 30%;
            left: 15%;
            opacity: 0.05;
            font-size: 80px;
            transform: rotate(-30deg);
            z-index: -1;
        }

        /* Header */
        header {
            position: fixed;
            top: -90px;
            left: 0;
            right: 0;
            text-align: center;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            vertical-align: top;
            border: none !important;
        }
        .logo {
            width: 140px;
            height: 55px;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
        }
        .company-name small {
            font-size: 14px;
            direction: rtl;
            display: block;
        }
        .label {
            font-weight: bold;
            background-color: #000;
            color: #fff;
            font-size: 16px;
            border-radius: 5px;
            padding: 3px 12px;
            display: inline-block;
            margin-top: 5px;
        }

        /* Table */
        main {
            margin-top: 5px;
        }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; font-size: 11px; }
        th { background: #f5f5f5; font-weight: bold; }
        .text-right { text-align: right; }
        tfoot th {
            background: #e9ecef;
            font-weight: bold;
        }


        .top-hr {
            border: 0;
            border-top: 3px solid lightblue;
            margin: 0;
        }
        @page {
            size: A4 landscape;
            margin: 100px 40px 12px 40px; /* bottom reduced */
        }



         /* table UI design  */

         #info_table {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;

        }

        #info_table td,
        #info_table th {
            border: 1px solid #ddd;
            padding: 8px;
        }

        #info_table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        #info_table tr:hover {
            background-color: #ddd;
        }

        #info_table th {
            padding-top: 5px;
            padding-bottom: 5px;
            text-align: center;
            background-color: #EAEDED;
            color: black;
            border-top: 1px solid lightgrey;    /* Keep horizontal lines */
            border-bottom: 1px solid lightgrey; /* Keep horizontal lines */
            border-left: none;              /* Remove vertical lines */
            border-right: none;             /* Remove vertical lines */
        }

        #info_table td {
            padding-top: 5px;
            padding-bottom: 5px;
            text-align: center;

             border-top: 1px solid lightgrey;    /* Keep horizontal lines */
            border-bottom: 1px solid lightgrey; /* Keep horizontal lines */
            border-left: none;              /* Remove vertical lines */
            border-right: none;             /* Remove vertical lines */

        }


    </style>
</head>
<body>
    <!-- Watermark -->
    <div class="watermark">{{ $company->comp_name_en ?? 'Company' }}</div>

    <!-- Header -->
    <header>
        <table class="header-table">
            <tr>
                <td style="width: 20%;">
                    <p style="font-size: 12px; margin: 0;">Generated on: {{ $current_datetime }}</p>
                </td>
                <td style="width: 60%; text-align: center;">
                    <div class="company-name">
                        {{ $company->comp_name_en ?? 'Company Name' }}
                        {{-- <small>{{ $company->comp_name_arb }}</small> --}}
                    </div>
                    <p style="font-size: 12px; margin: 0;">{{ $company->comp_address ?? 'Address' }}</p>
                </td>
                <td style="width: 20%; text-align: left;">
                     <img src="{{ asset($company->com_logo) }}"  alt="Not Found" width="210px" height="70px">
                </td>
            </tr>
        </table>

        <!-- Colorful divider line -->
        <hr class="top-hr">
    </header>

    <!-- Table -->
    <main>
        <div style="text-align:left; font-weight:bold; font-size:15px; margin-bottom:8px;">
            Supplier Outstanding Report
        </div>


        <table id="info_table">

                <tr>
                    <th>SN</th>
                    <th>Supplier Name</th>
                    <th>Address</th>
                    <th>VAT No.</th>
                    <th>Opening At</th>
                    <th>Contact Person</th>
                    <th>Contact Details</th>
                    <th  class="text-right">Opening Balance</th>
                    <th  class="text-right">Current Balance</th>
                </tr>

            <tbody>
                @php
                    $total_current_balance =0;
                    $total_opening_balance =0;
                @endphp
                @foreach($records as $index => $item)
                @php
                    $total_current_balance += $item->current_balance;
                    $total_opening_balance += $item->opening_balance;

                @endphp

                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $item->supplier_name  }}</td>
                    <td>{{ $item->supplier_address }}</td>
                    <td  style="text-align: center;">{{ $item->vat_no }}</td>
                    <td>{{ $item->opening_date }}</td>
                    <td> {{ $item->contact_person }} </td>
                    <td>  {{ $item->contact_person_phone }} <br> {{  $item->contact_person_email }}</td>
                     <td class="text-right">
                        {{   number_format($item->opening_balance, 2)  }}
                    </td>
                    <td class="text-right">
                         {{   number_format($item->current_balance, 2) }}
                    </td>

                </tr>
                @endforeach
            </tbody>
                 <tr  style=" background-color:#C5AB57EE; color:black; font-weight:bold;">

                    <th colspan="7" class="text-right">Total:</th>
                    <th class="text-right">{{ number_format($total_opening_balance, 2) }}</th>
                    <th class="text-right">{{ number_format($total_current_balance, 2) }}</th>
                </tr>
         </table>
    </main>


    <!-- Footer -->
    <div class="footer" style="position:fixed; bottom:12px; left:0; right:0; text-align:center; font-size:10px; color:#666;">System Generated Statement Authorized by Accounts Department || <span class="footer-date">{{ $current_datetime }}</span></div>
</body>
</html>
