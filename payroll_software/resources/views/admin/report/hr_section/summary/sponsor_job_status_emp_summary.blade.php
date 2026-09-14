<!DOCTYPE html>
<html lang="en">
<title>Emp. Summary(Sopnsor)</title>
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
                margin: 0 auto 10px;
            }

            @page {
                size: A4 portrait;
                margin: 15mm 0mm 10mm 10mm;
                /* top, right,bottom, left */

            }
            a[href]:after {
                content: none !important;
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
            width: 98%;
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
            font-size:16px;
        }
        .title_left__part{
            text-align: left;
            font-size:14px;
        }
        .address {
            font-size:12px;
        }
        .print__part{
            text-align: right;
            font-size:12px;
            padding-right:20px;
        }

        /* table part */
        .table__part {
            display: flex;
        }

        table {
            width: 95%;
            padding: 0px;
            align:center
        }

        #report_table {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;

        }

        #report_table td,
        #report_table th {
            border: 1px solid #ddd;
            padding: 8px;
        }

        #report_table tr:nth-child(even) {
            background-color: #ECF0F1;
        }

        #report_table tr:hover {
            background-color: #ddd;
        }

        #report_table th {
            padding-top: 5px;
            padding-bottom: 5px;
            text-align: center;
            background-color: #B3E6FA;
            color: black;
        }

        .td__s_n {
            font-size: 14px;
            color: black;
            text-align: center;

        }

        .td__title_name {
            font-size: 14px;
            color: black;
            text-align: left;
            font-weight: 100;
            padding-left: 10px;
        }
        .td__total_emp {
            font-size: 14px;
            color: black;
            font-weight: 400;
            text-align: center;
            text-transform:capitalize
        }



    </style>
    <!-- style -->
</head>

<body>
    <div class="main__wrap">
        <!-- header part-->
        <section class="header__part">

            <div class="title_left__part">

            </div>

            <div class="title__part">
                <h6>{{$company->comp_name_en}} <small>{{$company->comp_name_arb}} </small> </h6>
                <address class="address">
                    {{$company->comp_address}}
                </address>
            </div>
            <div class="print__part">
                <p> <strong>Print Date:</strong> {{ Carbon\Carbon::now()->format('d/m/Y') }} </p>
                <button type="" onclick="window.print()" class="print__button">Print</button>
            </div>
        </section>
        <!-- table part -->
         <h4 style="text-align: center"> {{$report_title}} </h4>
        <section class="table__part">
            <table id="report_table">
                <thead>
                <tr>
                  <th > <span>S.N</span> </th>
                  <th> <span>Sponsor Name</span> </th>
                  <th> <span>Active</span> </th>
                  <th> <span>Vacation</span> </th>
                  <th> <span>Total(Active+Vacation)</span> </th>
                  <th> <span>Runaway</span> </th>
                  <!--<th> <span>Total</span> </th>-->

                </tr>
                </thead>
                <tbody>
                  @php  $total_emp = 0;
                        $total_active_emp = 0;
                        $total_vacation_emp = 0 ;
                        $total_runaway_emp = 0;
                  @endphp

                @foreach ($report_records as $arecord)
                   @php
                      $total_emp += $arecord->active_emp + $arecord->vacation_emp  + $arecord->runaway_emp ;
                      $total_active_emp += $arecord->active_emp   ;
                      $total_vacation_emp +=  $arecord->vacation_emp   ;
                      $total_runaway_emp +=  $arecord->runaway_emp ;
                   @endphp

                    <tr >
                    <td class="td__s_n"> {{ $loop->iteration }}</td>
                    <td class="td__title_name"> {{ Str::upper($arecord->spons_name) }}</td>
                    <td class="td__total_emp"> {{ $arecord->active_emp == 0 ? '-' : $arecord->active_emp  }} </td>
                    <td class="td__total_emp"> {{ $arecord->vacation_emp == 0 ? '-' : $arecord->vacation_emp  }} </td>
                    <td class="td__total_emp"> {{ $arecord->active_emp + $arecord->vacation_emp  }} </td>
                    <td class="td__total_emp"> {{ $arecord->runaway_emp == 0 ? '-' : $arecord->runaway_emp  }} </td>
                    <!--<td class="td__total_emp"> {{ $arecord->active_emp + $arecord->vacation_emp  + $arecord->runaway_emp  }} </td>-->
                    </tr>
                @endforeach
                <tr >
                  <td colspan ="2" class="td__total_emp">Total </td>
                  <td class="td__total_emp">  {{ $total_active_emp }} </td>
                  <td class="td__total_emp">  {{ $total_vacation_emp }} </td>
                  <td class="td__total_emp">  {{ $total_active_emp + $total_vacation_emp }} </td>
                  <td class="td__total_emp">  {{ $total_runaway_emp }} </td>
                  <!--<td class="td__total_emp">  {{ $total_emp }} </td>-->
                 </tr>
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
