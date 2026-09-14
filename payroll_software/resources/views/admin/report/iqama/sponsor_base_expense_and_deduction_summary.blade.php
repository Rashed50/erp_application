
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
                max-width: 90%;
                margin: 0 auto 10px;
            }

            @page {
                size: A4 portrait;
                margin: 15mm 5mm 5mm 15mm;
                /* top, right,bottom, left */

            }
            a:link {
             text-decoration: none;
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
            width: 95%;
            margin: 10px auto;
        }

        .header__part {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .title__part h6 {
            color: rgb(38, 104, 8);
            text-align: center;
            font-size:18px;
        }

        .title__part{
            text-align: center;
            font-size:18px;
        }
        .title_left__part{
            text-align: left;
            font-size:11px;
        }
        .address {
            font-size:13px;
        }
        .print__part{
            text-align: right;
            font-size:10px;
        }

        /* table part */
        .table__part {
            display: flex;
        }

        table {
            width: 100%;
            padding: 0px;
        }

        table,
        tr {
          border: 1px ridge gray;
            border-collapse: collapse;
            margin: 0px;
            padding: 0px;
        }

        table th {
            font-size: 10px;
            border: 1px ridge gray;
            font-weight: bold;
            padding: 5px;
            color: #000;
            text-transform:capitalize;
            font-family: "Times New Roman", Times, serif;
        }

        table td {
            text-align: center;
            font-size: 10px;
            border: 1px ridge gray;
            padding: 5px;
            margin: 0px;
            text-transform:capitalize;
            font-family: "Times New Roman", Times, serif;
            /* Top,Right,Bottom,left */
        }
 

        .td__s_n {
            font-size: 10px;
            color: black;
            text-align: center;

        } 
        .td__sponsor_name {
            font-size: 10px;
            color: black;
            font-weight: 100;
            text-align: left;
            padding-left:5px;
            text-transform:capitalize
        }
          
        .td__amount {
            font-size: 10px;
            color: black;
            font-weight: 100;
            text-align: right;
            padding-right:10px;
        }
        .td__total_amount {
            font-size: 10px;
            color: black;
            font-weight: bold;
            text-align: right;
            padding-right:10px;
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
                {{-- <p> <strong> Project : {{$projectName}} </strong></p> --}}
                <p> <strong> Reporting Month : {{$month}} {{$year}} </strong></p>
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
                <p> <strong>Print Date</strong> {{ Carbon\Carbon::now()->format('d/m/Y') }} </p>
                <button type="" onclick="window.print()" class="print__button">Print</button>
            </div>
        </section>

        <h4 style="text-align:center">Sponsor Base Iqama Renewal & Deduction Summary for a Month</h4><br>
         <!-- table part -->
         <section class="table__part">
            <table>
                <thead>
                    <tr>
                        <th> <span>S.N</span> </th> 
                        <th>SPONSOR NAME</th>
                        <th>NO. OF EMPLOYEES </th>
                        <th> <span>TOTAL IQAMA EXPENSE</span> </th>
                        <th> <span>DEDUCTION FROM SALARY </span> </th>
                         <th> <span>BALANCE</span> </th>
                         <th> <span>REMARKS</span> </th>

                    </tr>
                   

                </thead>
                <tbody>
                    @php
                        $ground_total_emp = 0;
                        $ground_total_expense = 0;
                        $ground_total_deduction = 0;
                    @endphp
                    @foreach ($sponsor_list as $sp)
                    @php
                        $ground_total_emp += $sp->total_emp  ;
                        $ground_total_expense += $sp->total_expense ;
                        $ground_total_deduction += $sp->total_deduction;
                    @endphp
                    <tr>
                        <td class="td__s_n"> <span>{{ $loop->iteration }}</span> </td> 
                        <td class="td__sponsor_name"> <span>{{ $sp->spons_name }}</span> </td> 
                        <td class="td__amount"> <span>{{ $sp->total_emp }}</span> </td> 
                        <td class="td__amount"> <span>{{number_format( $sp->total_expense, 2, '.', ',') }}</span> </td>
                        <td class="td__amount"> <span>{{number_format( $sp->total_deduction, 2, '.', ',') }} </span> </td>
                        <td class="td__amount"> <span>{{number_format( $sp->total_expense - $sp->total_deduction, 2, '.', ',') }}</span></td>
                        <td class="td__sponsor_name">  </td> 

                    </tr>
                    @endforeach
                    <tr>
                        <td class="td__total_amount" colspan="2"> <strong> TOTAL </strong> </td>
                        <td class="td__total_amount"> <strong>{{number_format(  $ground_total_emp, 2, '.', ',') }}</strong> </td>
                        <td class="td__total_amount"> <strong>{{number_format(  $ground_total_expense, 2, '.', ',') }}</strong> </td>
                        <td class="td__total_amount"> <strong>{{number_format(  $ground_total_deduction, 2, '.', ',') }}</strong></td>
                        <td class="td__total_amount"> <strong>{{number_format( $ground_total_expense - $ground_total_deduction, 2, '.', ',') }}</strong></td>
                        <td class="td__total_amount">  </td> 
                    </tr>
                </tbody>
            </table>
        </section>
        <!-- ---------- -->



    <section>
        <br><br>
        {{-- Officer Signature --}}
        <div class="row" style="padding-top: 20px;">
            <div class="officer-signature" style="display: flex; justify-content:space-between; font-size:9px">
                <p>Prepared By<br/><br/><br></p>
                <p>Checked By</p>
                <p>Verified By</p>
            </div>
        </div>
        {{-- Officer Signature --}}
    </section>
    </div>
</body>

</html>









