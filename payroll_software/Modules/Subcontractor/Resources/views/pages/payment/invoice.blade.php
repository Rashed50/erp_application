<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Payment Receipt </title>
    <style>

        @page {
            size: A4 portrait;
            margin: 100px 40px 60px 40px;

            @bottom-left {
                content: "Generated on:-";
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
            height: 40px;
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
            margin-top: 60px;
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

         .signature-table {
            width: 100%;
            margin-top: 60px;
            position: relative;
            z-index: 1;
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

                </td>
                <td style="width: 35%; text-align: end;">
                    {{-- <div class="company-title">ASLOOB BEDAA CO.</div>
                    <div class="subtitle">12611, {{ $company->comp_address }}</div>
                    <div class="subtitle">+966 54 765 7236, {{ $company->comp_phone1 }} </div>
                    <div class="subtitle">{{ $company->comp_email1 }} </div> --}}
                    {{-- <img src="{{ public_path('contents/admin/assets/images/logo_new.png') }}"
                        style="width: 130px; height: auto;"> --}}
                    <img src="{{ asset($company->com_logo) }}"  alt="" width="184px" height="80px">
                </td>
            </tr>
        </table>

        <hr class="top-hr">
    </header>

    <div class="content">
        <h2 style="text-align: left; font-size:16px; font-weight:bold;">Payment Receipt</h2>

        <table class="info-table">
            <tr>
                <!-- Expence By -->
                <td style="width: 70%;">
                    <strong>Paid To</strong><br>
                    <span style="margin-top: 5px;"> Name: {{ $invoice_data['subcontractor']->subcon_name }} </span><br>
                    <span> Iqama No: {{ $invoice_data['subcontractor']->iqama_no }}</span><br>
                    <span> Passport: {{ $invoice_data['subcontractor']->passfort_no }}</span><br>
                    <span> Contact Phone: {{ $invoice_data['subcontractor']->mobile_no }}</span><br>
                    <span> Email: {{ $invoice_data['subcontractor']->mobile_no }}</span><br>

                </td>

                <!-- Reference -->

                <td style="width: 30%;">
                    <span>Invoice No: {{ $invoice_data['invoice_no'] }}</span><br>
                    <span>Payment Method:
                        {{ $invoice_data['payment_method'] == 1 ? 'Cash' : 'Bank' }}</span><br>
                    <span>Paid Date: {{ $invoice_data['payment_date'] }} </span><br>
                </td>
            </tr>
        </table>

        <hr class="content-hr">
        <br><br>

        <table style="width: 100%; border-collapse: collapse; font-size: 14px; margin-top: 20px; margin-bottom: 10px;">
            <tr>
                <td style="width: 48%; vertical-align: top; padding: 5px; border: none;">
                    <strong>Remarks</strong><br>
                    <span style="margin-top: 5px;margin-right: 10px; text-align:justify">
                        {{ $invoice_data['remarks'] }}
                      Payment Confirmation <br>
                     Employee's Salary has been processed for the month of {{ $invoice_data['month_name'] }},{{ $invoice_data['year']  }}
                     and total amount SAR {{  $invoice_data['total_invoice_amount'] }}. Total Paid amount including today's amount SAR {{ $invoice_data['total_paid']  }}
                     Remaining amount SAR {{ $invoice_data['total_invoice_amount']- $invoice_data['total_paid']  }} will be paid.
                    </span><br>
                </td>
                @php
                    $total_invoice_amount = $invoice_data['total_invoice_amount'];
                    $vat_amount = $invoice_data['vat_amount'];
                    $today_pay_amount =  $invoice_data['pay_amount'] ;//+ $vat_amount;
                @endphp
                <td style="width: 2%;"></td>

                <td style="width: 60%; vertical-align: top; padding: 5px; border: none;">
                    <table style="width: 100%; font-size: 13px; border-collapse: collapse;">
                        <tr style="font-weight: bold;">
                            <td style="padding: 2px; ">{{ $invoice_data['month_name'] }},{{  $invoice_data['year'] }} Invoice  Amount</td>
                            <td style="padding: 2px; text-align:right;" >
                                {{ number_format(round($total_invoice_amount, 2), 2, '.', ',') }}
                            </td>
                        </tr>
                        <tr style="border-bottom: 1px solid #999;font-weight: bold;">
                            <td style="padding: 2px;">Today Paid</td>
                            <td style="padding: 2px; text-align:right;">
                                {{ number_format(round($today_pay_amount, 2), 2, '.', ',') }} </td>
                        </tr>
                        {{-- <tr style="font-weight: bold;">
                            <td style="padding: 2px;">VAT</td>
                            <td style="padding: 2px;" class="text-right">
                                {{ number_format(round($vat_amount, 2), 2, '.', ',') }}</td>
                        </tr> --}}
                        <tr style="border-bottom: 1px solid #999;font-weight: bold;">
                            <td style="padding: 2px;">Remaining Balance</td>
                            <td style="padding: 2px; text-align:right;">
                                {{ number_format(round($total_invoice_amount - $invoice_data['total_paid'], 2), 2, '.', ',') }}</td>
                        </tr>

                    </table>

                </td>
            </tr>


            <tr>
                <td colspan="3" style="font-weight: bold"><br><br><br></td>
            </tr>
            <tr>
                <td colspan="3" style="font-weight: bold">In Word: {{  $invoice_data['amount_in_words'] }}</td>
            </tr>
        </table>
        <br><br><br> <br><br><br>
         

        <table class="signature-table" style="border: none;">
                <tr style="border: none;">
                    <td style="width: 33%; text-align: left; border: none;">
                        <p><strong>Received By</strong></p>
                        <p>{{ $invoice_data['subcontractor']->subcon_name }}</p>
                    </td>
                    <td style="width: 33%; text-align: center; border: none;">
                        <p><strong>Verified By</strong></p>
                        <p>  </p>
                    </td>
                    <td style="width: 33%; text-align: right; border: none;">
                         <p><strong>Paid By</strong></p>
                        <p>{{ $prepared_by  }}</p>
                    </td>
                </tr>
        </table>

    </div>

    <footer>
        <p>&copy; {{ date('Y') }} Asloob Bedaa Contracting Company All rights reserved.</p>
     </footer>

</body>

</html>
