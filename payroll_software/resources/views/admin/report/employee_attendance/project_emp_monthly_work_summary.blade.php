<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pro.Emp. Work Summary
    </title>
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
                margin: 15mm 0mm 10mm 0mm;
                /* top, right,bottom, left */

            }
            .print__button {
                  visibility: hidden;
               }

               th.td__friday, td.td__friday {
                background-color: #AED6F1;
                -webkit-print-color-adjust: exact;
            }

            /* th.td__friday {
                background-color: #AED6F1;
                -webkit-print-color-adjust: exact;
            } */

            table { page-break-after:auto }
            tr    { page-break-inside:avoid; page-break-after:auto }
            td    { page-break-inside:avoid; page-break-after:auto }
            thead { display:table-header-group }
            tfoot { display:table-footer-group }

        }


        .main__wrap {
            width: 90%;
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
            font-size:12px;
        }
        .title_left__part{
            text-align: left;
            font-size:10px;
        }
        .address {
            font-size:10px;
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
            border: 1px solid #333;
            border-collapse: collapse;
            margin: 0px;
            padding: 0px;
        }

        /* table tr {} */

        table th {
            font-size: 10px;
            border: 1px solid #333;
            font-weight: bold;
            color: #000;

        }

        table td {
            text-align: center;
            font-size: 10px;
            border: 1px solid #333;
            padding: 0px;
            margin: 0px;
            height:20px;

            /* Top,Right,Bottom,left */
        }
        .td__friday {
            background-color: #AED6F1;
        }

        .td__s_n {
            font-size: 10px;
            color: black;
            text-align: center;
            width:30px;
        }

        .td__employee_id {
            font-size: 10px;
            color: red;
            text-align: center;
            font-weight: bold;
            padding: 0px;
            /* width:30px; */
        }
        .td__emplyoee_name {
            font-size: 9px;
            color: blue;
            font-weight: 200;
            text-align: left;
            padding-left: 2px;
            /* width:130px; */

        }

        .td__iqama {
            font-size:9px;
            color: black;
            font-weight: 100;
            text-align: center;
            /* width:50px; */
        }

        .td__emp_trade {
            font-size: 9px;
            color: black;
            font-weight: 100;
            text-align: center;
            padding-left: 2px; 
            /* width:80px;  */
        }

        .td__day{
            font-size: 9px;
            color: black;
            font-weight: 100;
            text-align: center;
            padding: 0px;
            margin:0px; 
        }
        .td__absent{
            font-size: 9px;
            color: red;
            font-weight: bold;
            text-align: center;
            padding: 0px;
            margin:0px; 
            
        }

        .td__total {
            font-size: 10px;
            color: black;
            text-align: center;
            font-weight: bold;
            padding: 0px;
        }

        a:link {
            color: white;
            background-color: white;
            text-decoration: none;
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
                <p> Attendance Work Hours Summary Month of <strong class="td__red__color"> {{ $monthName }}, {{$year}} </strong> </p>
                <p> <strong>Project :</strong> {{$projectName}}</p>
                <p> <strong>Sponser :</strong> {{ $sponserName ?? 'All Sponser'}}</p>
            </div>
            <!-- title -->
            <div class="title__part">
                <h6>{{$company->comp_name_en}} <small>{{$company->comp_name_arb}} </small> </h6>
                <address class="address">
                    {{$company->comp_address}}
                </address>
                <br>
                <address class="address">                    
                    @if($working_shift == null)
                           Both Day & Night Shift
                    @elseif($working_shift == 0)
                       Day Shift
                    @elseif($working_shift == 1)
                            Night Shift                    
                    @endif
                </address>
               
            </div>
            <!-- print button -->
            <div class="print__part">
                <p> <strong>Print Date</strong> {{ Carbon\Carbon::now()->format('d/m/Y') }} </p>
                <button type="" onclick="window.print()" class="print__button">Print</button>
            </div>
        </section>
        <!-- table part -->
        <section class="table__part">
            <table>
                <thead>
                    <tr>
                        <th>S.N</th>
                        <th>ID</th>
                        <th>Name </th>
                        <th>Iqama </th>
                        <th>Trade </th> 
                        <th>Total Days</th>
                        <th>Basic Hours</th>  
                        <th>Over Time</th>
                        <th>Total Hours</th>                         
                    </tr>
                </thead>
                <tbody>

                    @php
                   
                    $grossWorkingHours = 0;
                    $gross_over_time = 0;
                    $grand_total = 0;
                    @endphp

                    @foreach($records as $emp)
                            @php  
                            $grossWorkingHours += $emp->total_work_hours  ;
                            $gross_over_time += $emp->overtime;
                            $grand_total +=  $emp->total_work_hours + $emp->overtime;

                            @endphp
                     
                    <tr style="border-bottom:0;">

                        <td class="td__s_n"> {{ $loop->iteration }}</td>
                        <td class="td__employee_id"> {{$emp->employee_id}}</td>
                        <td class="td__emplyoee_name"> {{ Str::limit($emp->employee_name,20) }} </td>
                        <td class="td__iqama">{{$emp->akama_no}}</td>
                        <td class="td__emp_trade">{{ Str::limit( $emp->catg_name,15) }} </td> 
                        <td  class="td__total">{{$emp->total_days}}</td>
                        <td  class="td__total">{{$emp->total_work_hours}}</td>
                        <td class="td__total">{{$emp->overtime}}</td>
                        <td  class="td__total">{{ $emp->total_work_hours + $emp->overtime }}</td>
                        
                    </tr>
                    @endforeach                    
                    <tr>
                           <td colspan="6" class="td__total">Total </td>
                            <td class="td__total">{{ $grossWorkingHours }}</td>
                             <td class="td__total">{{ $gross_over_time}}</td>
                            <td class="td__total">{{ $grand_total }}</td>
                             

                    </tr>
                </tbody>
                </tbody>
            </table>
        </section>
        <!-- ---------- -->
        <br><br>
    <section>
        {{-- Officer Signature --}}
        <div class="row" style="padding-top: 20px;">
            <div class="officer-signature" style="display: flex; justify-content:space-between; font-size:9px">
              <p>   <b> {{$prepared_by}} </b> <br> Prepared By</p>
                <p><br>Checked By</p>
                <p><br>Verified By</p>
            </div>
        </div>
        {{-- Officer Signature --}}
    </section>
    </div>
</body>

</html>