<!DOCTYPE html>
<html lang="en">

<head>
   <title>Inv-Summary Report</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- style -->
    <style>
        * {
            margin: 0;
            padding: 0;
            outline: 0;
        }


        @media print {
            .container {
                max-width: 98%;
                margin: 5 auto ;


            }

            @page {
                size: A4 landscape;
                margin: 15mm 5mm 10mm 15mm;
                /* top, right,bottom, left */

            }

            .print__button {
                visibility: hidden;
            }

            table { page-break-after:auto }
            tr    { page-break-inside:avoid; page-break-after:auto }
            td    { page-break-inside:avoid; page-break-after:auto }
            thead { display:table-header-group }
            tfoot { display:table-footer-group }

        }


        .main__wrap {
            width: 99%;
            margin: 10px auto;
        }

        .header__part {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .title__part {
            text-align: center;
            font-size: 12px;
        }

        .title_left__part {
            text-align: left;
            font-size: 12px;
        }

        .address {
            font-size: 12px;
        }

        .print__part {
            text-align: right;
            font-size: 10px;
            padding-right: 20px;
        }

        /* table part */
        .table__part {
            display: flex;
        }

        table {
            width: 98%;
            padding: 0px;
            align: center
        }

        table,
        tr {
            border: 1px ridge gray;
            border-collapse: collapse;
            margin: 0px;
            padding: 0px;
        }

        table th {
            font-size: 12px;
            border: 1px ridge gray;
            font-weight: bold;
            padding: 5px;
            color: #000;
            text-transform: capitalize;
            font-family: "Times New Roman", Times, serif;
        }

        table td {
            text-align: center;
            font-size: 11px;
            border: 1px ridge gray;
            padding: 5px;
            margin: 0px;
            text-transform: capitalize;
            font-family: "Times New Roman", Times, serif;
            /* Top,Right,Bottom,left */
        }


        .td__s_n {
            font-size: 11px;
            color: black;
            text-align: center;

        }

        .td__title_name {
            font-size: 11px;
            color: black;
            text-align: left;
            font-weight: 100;
            padding-left: 10px;
        }

        .td__amount {
            font-size: 11px;
            color: black;
            font-weight: 200;
            padding-right: 5px;
            text-align: right;
        }
        .td__total_amount {
            font-size: 12px;
            color: black;
            font-weight: bold;
            padding-right: 5px;
            text-align: right;
        }
    </style>
    <!-- style -->
</head>

<body>
    <div class="main__wrap">
        <!-- header part-->
        <section class="header__part">
            <!-- date -->
            <div class="title_left__part">
                <p> <strong> Invoice Summary Report </strong></p>
            </div>
            <!-- title -->
            <div class="title__part">
                <h6>{{$company->comp_name_en}} <small>{{$company->comp_name_arb}} </small> </h6>
                <address class="address">
                    {{$company->comp_address}}
                </address>
            </div>
            <!-- print button -->
            <div class="print__part">
                <p> <strong>Print Date:</strong> {{ Carbon\Carbon::now()->format('d/m/Y') }} </p>
                <button type="" onclick="window.print()" class="print__button">Print</button>
            </div>
        </section>
        <!-- table part -->
        @foreach ($project_reports as $arecord)
        <section class="table__part">

                @php
                    $records = $arecord->invoice_records;
                @endphp
                <table>
                    <caption> <b>{{$arecord->proj_name}}</b></caption>
                    <thead>
                        <tr>
                            <th>S.N</th>
                            <th>Project Name</th>
                            <th>Invoice No</th>
                            <th>Contract Ref.</th>
                            <th>Month,Year</th>
                            <th>Invoice Date</th>
                            <th>Inv. Amount</th>
                            <th>VAT</th>
                            <th>Total with VAT</th>
                            <th>Retention</th>
                            <th>Receivable Amount</th>
                            <th>Status</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $total_invoice_amount = 0;
                            $total_vat_amount = 0;
                            $total_retention_amount = 0;
                            $total_received_amount = 0;
                            
                        @endphp

                        @foreach ($records as $itm)
                        @php
                            $total_invoice_amount += $itm->invoice_amount ;
                            $total_vat_amount += $itm->vat_amount ;
                            $total_retention_amount += $itm->retention_amount ;
                            $total_received_amount += $itm->receivable_amount ;
                         @endphp
                        <tr>
                            <td class="td__s_n"> {{ $loop->iteration }}</td>
                            <td class="td__title_name">  {{ $itm->proj_name }}</td>
                            <td class="td__title_name">  {{ $itm->invoice_no }}</td>
                            <td class="td__title_name">  {{ $itm->contract_reference }}</td>
                            <td class="td__title_name"> {{ $itm->month_name }}, {{ $itm->year }} </td>
                            <td class="td__title_name"> {{ $itm->submit_date }} </td>
                            <td class="td__amount"> {{ $itm->invoice_amount }} </td>
                            <td class="td__amount"> {{ $itm->vat_amount }}</td>
                            <td class="td__amount"> {{ $itm->invoice_amount + $itm->vat_amount }}</td>
                            <td class="td__amount"> {{ $itm->retention_amount }}</td>
                            <td class="td__amount"> {{ $itm->receivable_amount }}</td>
                            <td class="td__title_name"> {{ $itm->invoice_status == 0 ? 'Pending' : 'Received' }}</td>
                            <td class="td__title_name"> {{ $itm->remarks }}</td>
                          
                            {{-- <a target="_blank" href="{{ url('${ value.advance_paper}') }}" class="btn btn-success">View </a>                              --}}
                        </tr>
                        @endforeach
                        <tr>
                            <td  class="td__total_amount" colspan="6"> Total = </td>
                            <td class="td__total_amount"> {{ $total_invoice_amount}} </td>
                            <td class="td__total_amount"> {{$total_vat_amount }} </td>
                            <td class="td__total_amount"> {{ $total_invoice_amount +$total_vat_amount  }}  </td>
                            <td class="td__total_amount"> {{ $total_retention_amount}} </td>
                            <td class="td__total_amount"> {{ $total_received_amount}} </td>

                        </tr>
                    </tbody>
                    </tbody>
                </table>

        </section>
        <br><br>
        @endforeach
        <!-- ---------- -->
        <br><br><br>
        <section>
            {{-- Officer Signature --}}
            <div class="row" style="padding-top: 20px; padding-right:30px;">
                <div class="officer-signature" style="display: flex; justify-content:space-between; font-size:11px">
                    <p>Prepared By<br><br><br> {{   $login_name}} </p>
                    <p>Checked By</p>
                    <p>Verified By</p>
                </div>
            </div>
            {{-- Officer Signature --}}
        </section>
    </div>
</body>

</html>
