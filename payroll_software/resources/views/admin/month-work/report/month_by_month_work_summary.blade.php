<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Salary Summary</title>
    <!-- style -->
    <style>




        body {
            overflow: hidden;
        }



        * {
            margin: 0;
            padding: 0;
            outline: 0;
        }


        @media print {
            .container {
                max-width: 96%;
                margin: 0 auto 10px;
            }
            @page {
                size: A4 landscape;
                margin: 15mm 5mm 10mm 0mm;
                /* top, right,bottom last value was= 10, left */

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
        /*   End of Page Print Setting */


        .main__wrap {
            width: 90%;
            margin: 20px auto;
        }

        .header__part {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .title__part {
            text-align: center;
        }

        .table__part {
            display: flex;
        }

        table {
            width: 100%;
            padding: 5px;
        }

        #employeeinfo {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;

        }

        #employeeinfo td,
        #employeeinfo th {
            border: 1px solid #ddd;
            padding: 4px;
            font-size: 13px;
        }

        #employeeinfo tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        #employeeinfo tr:hover {
            background-color: #ddd;
        }

        #employeeinfo th {
            padding-top: 5px;
            padding-bottom: 5px;
            text-align: center;
            background-color: #EAEDED;
            color: black;
        }

        #employeeinfo td {
            padding-top: 5px;
            padding-bottom: 5px;
            /*text-align: center;*/

        }
        .td__sn{
             text-align: center;
        }
         .td__project{
            padding-left:5px;
            text-align: left;
        }
        .td__amount {

            text-align: right;
            font-weight:normal;
            margin-right:5px;

        }
        .td__total {

            text-align: right;
            font-weight:bold;
            margin-right:5px;

        }
        .tr__header{
            text-align: center;
            font-weight:bold;
            background: rgb(40, 120, 185);;
            color: black;
     }

     .tr__ground_total{
            text-align: right;
            font-weight:bold;
            margin-right:5px;
            background:  rgba(197, 171, 87, 0.932);
            color: black;
     }
    </style>
    <!-- style -->
</head>

<body>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

    <div class="main__wrap">
        <!-- header part-->
        <section class="header__part">
            <!-- date -->
            <div  class="date__part">
            </div>

            <!-- title -->
            <div class="title__part">
                <h4>{{$company->comp_name_en}} <small>{{$company->comp_name_arb}} </small> </h4>
                <address class="address">
                    {{$company->comp_address}}
                </address>
            </div>
            <!-- print button -->
            <div class="print__part">
               <p> <strong>Print </strong> {{ Carbon\Carbon::now() }} </p>
                <button type="" onclick="window.print()" class="print__button">Print</button>
            </div>
        </section>
        <!-- table part -->
        <br>  <h3 style="text-align:center; color:black"> Hourly Statement </h3> <br>
        <section class="table__part">
            <table id="employeeinfo">
            <tbody>
                    <tr class="tr__header">
                        <td >S.N</td>
                        <td >Project Name </td>
                        @foreach($month_years as $my)
                        <td> {{ date("F", mktime(0, 0, 0, $my['month'], 10))   }} <br>{{ $my['year'] }} </td>
                        @endforeach
                        <td >Total Hours</td>
                     </tr>
                    @php
                      $month_base_total_amount = array_fill(0,count($month_years),0);

                    @endphp

                    @foreach($final_records as $ar)
                    @php
                      $records = $ar->records;
                      $total_hours = 0;
                      $counter = 0;
                    @endphp
                    <tr>
                        <td class="td__sn"> {{$loop->iteration}}</td>
                        <td class="td__project">{{$ar->proj_name }}</td>
                        @foreach($records as $ar)
                        @php
                            $total_hours += $ar['total_hours'];
                            $month_base_total_amount[$counter++] += $ar['total_hours'];
                        @endphp
                            <td class="td__amount">{{  $ar['total_hours'] == 0 ? "-" : number_format( $ar['total_hours'] , 0, '.', ',') }}    </td>
                         @endforeach
                            <td class="td__amount">{{  $total_hours == 0 ? "-" : number_format( $total_hours , 0, '.', ',')  }}    </td>


                    </tr>
                    @endforeach
                    <tr class="tr__ground_total">

                        <td colspan="2" >TOTAL</td>
                        @php
                            $grand_total_amount = 0;
                        @endphp
                        @foreach($month_base_total_amount as $sp)
                            @php
                                $grand_total_amount += $sp;
                            @endphp
                            <td> {{ $sp <= 0 ? '-':  number_format( $sp , 0, '.', ',') }} </td>
                        @endforeach
                        <td>{{ $grand_total_amount <= 0 ? '-':  number_format( $grand_total_amount , 0, '.', ',') }}  </td>

                    </tr>
                </tbody>
            </table>
        </section>
        <!-- ---------- -->
         <section>
           {{-- Officer Signature --}}
            <div class="row" style="padding-top: 80px;">
                <div class="officer-signature" style="display: flex; justify-content:space-between; text-align: center; ">
                    <p>  <b>  ------------------------ <br> {{$login_name}} </b> <br> Prepared By  </p>
                    <p>  <b>  ------------------------ <br>   </b> Accounts/HR  </p>
                    <p>  <b>  ------------------------ <br>   </b> Approved By  </p>
                </div>
            </div>
            <p style="text-align: center; color:grey">Online System Generated Statement Authorized by Accounts Department</p>
        </section>

    </div>
</body>


</html>
