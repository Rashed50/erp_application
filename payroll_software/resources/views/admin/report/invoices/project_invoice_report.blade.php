<!DOCTYPE html>
<html lang="en">

<head>

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

        .td__total_emp {
            font-size: 11px;
            color: black;
            font-weight: 400;
            text-align: center;
            text-transform: capitalize
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
                <p> <strong> {{$report_title[0]}}     </strong></p>  
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
        <section class="table__part">
            <table>
                <thead>
                    <tr>
                        <th > <span>S.N</span> </th>
                        <th> <span>Project </span> </th>
                        <th> <span>Invoice No</span> </th>
                        <th> <span>Company  </span> </th>
                        <th> <span>Main Contractor</span> </th>
                        <th> <span>Date</span> </th>
                        <th> <span>Status</span> </th>
                        <th> <span>Grand Total</span> </th>
                        <th> <span>VAT</span> </th>
                        <th> <span>Total(VAT Incl.)</span> </th>
                        <th> <span>Retention</span> </th> 
                        <th> <span>Bill Amount</span> </th> 
                        
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $itm)
                    <tr>
                        <td class="td__s_n"> {{ $loop->iteration }}</td>
                        <td class="td__employee_id">  {{ $itm->proj_name }}</td>
                        <td class="td__emplyoee_info"> {{ $itm->invoice_no }} </td>
                        <td class="td__emplyoee_info"> {{ $itm->sb_comp_name }} </td>
                        <td class="td__emplyoee_info"> {{ $itm->en_name }} </td> 
                        <td class="td__emplyoee_info"> {{ $itm->submitted_date }}</td> 
                        <td class="td__emplyoee_info"> {{ $itm->status == 1 ? 'Pending' : 'Released' }}</td>  
                        <td class="td_amount">{{number_format($itm->items_grand_total_amount  , 2, '.', ',')}} </td>  
                        <td class="td_amount">{{number_format($itm->total_vat  , 2, '.', ',')}} </td>                          
                        <td class="td_amount">{{number_format($itm->total_amount  , 2, '.', ',')}} </td>  
                        <td class="td_amount">{{number_format($itm->total_retention  , 2, '.', ',')}} </td>  
                        <td class="td_amount">{{number_format(($itm->total_amount -  $itm->total_retention)  , 2, '.', ',')}} </td>  
  

                       </tr>
                    @endforeach
                </tbody>
                </tbody>
            </table>
        </section>
        <!-- ---------- -->
        <br><br><br>
        <section>
            {{-- Officer Signature --}}
            <div class="row" style="padding-top: 20px; padding-right:30px;">
                <div class="officer-signature" style="display: flex; justify-content:space-between; font-size:11px">
                    <p>Prepared By<br /><br /><br></p>
                    <p>Checked By</p>
                    <p>Verified By</p>
                </div>
            </div>
            {{-- Officer Signature --}}
        </section>
    </div>
</body>

</html>
