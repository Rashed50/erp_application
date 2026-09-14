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
                max-width: 70%;
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
        }


        .main__wrap {
            width: 70%;
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
            font-size:12px;
        }
        .address {
            font-size:12px;
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
            font-size: 12px;
            border: 1px ridge gray;
            font-weight: bold;
            padding: 5px;
            color: #000;
            text-transform:capitalize;
            font-family: "Times New Roman", Times, serif;
        }

        table td {
            text-align: center;
            font-size: 12px;
            border: 1px ridge gray;
            padding: 5px;
            margin: 0px;
            text-transform:capitalize;
            font-family: "Times New Roman", Times, serif;
            /* Top,Right,Bottom,left */
        }


        .td__s_n {
            font-size: 12px;
            color: black;
            text-align: center;

        }
 
        .td__trade_name {
            font-size: 12px;
            color: black;
            font-weight: 100;
            text-align: left;
            text-transform:capitalize
        }

        

        .td__amount {
            font-size:12px;
            color: black;
            font-weight: 100;
            text-align: center;
            padding-right:10px;
        }
        .td__total_amount {
            font-size:12px;
            color: black;
            font-weight: bold;
            text-align: center;
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
                <p> <strong>  Manpower Attendance Summary at {{$report_title[0]}}</strong></p>
                <p> <strong>  Project: {{$project_name}}</strong></p>
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
        <!-- table part -->
        <section class="table__part">
            <table>
                <caption> Day Shift Working Manpower Summary</caption>
                <thead>
                <tr>
                  <th > <span>S.N</span> </th>  
                  <th> <span>Trade Name</span> </th>
                  <th> <span>Total Employees</span> </th>                 
                  <th> <span>Present</span> </th>  
                  <th> <span>Absent</span> </th>                  
                </tr>
                </thead>
                <tbody>

                 <!--  Attendance Summary Day Shift-->
                 
                    @php 
                        $grand_total_present_emp = 0;
                        $grand_total_emp_in_project = 0;
                    @endphp

                  @foreach ($atten_day_manpower_records as $attendence)
                    @php   
                        $grand_total_present_emp +=$attendence->total_present_emp ;
                        $grand_total_emp_in_project +=$attendence->total_emp_in_project ;
                    @endphp
               
                  <tr style="text-align: center;">
                    <td class="td__s_n"> {{ $loop->iteration }}</td>                        
                    <td class="td__trade_name"> {{ $attendence->catg_name}}</td>
                    <td class="td__amount"> {{ $attendence->total_emp_in_project }} </td>
                    <td class="td__amount"> {{ $attendence->total_present_emp }} </td>
                     <td class="td__amount"> {{ ($attendence->total_emp_in_project - $attendence->total_present_emp) <= 0 ? "-" : ($attendence->total_emp_in_project - $attendence->total_present_emp) }} </td>
                 </tr>
                @endforeach  


                <tr>
                    <td class="td__total_amount" colspan="2"></td> 
                    <td class="td__total_amount" > {{ $grand_total_emp_in_project == 0 ? '' : $grand_total_emp_in_project}}</td> 
                    <td class="td__total_amount"  > {{ $grand_total_present_emp == 0 ? '' : $grand_total_present_emp }}  </td> 
                    <td class="td__total_amount"  > {{ ($grand_total_emp_in_project - $grand_total_present_emp) <= 0 ? "-" : $grand_total_emp_in_project - $grand_total_present_emp }}  </td>                
                </tr>

                <tr style="text-align: center;">
                    <td class="td__total_amount" colspan="4"> Night Shift Working Manpower Summary</td> 
                 </tr>
            <!--  Attendance Summary Night Shift-->

                    @php 
                        $grand_total_present_emp = 0;
                        $grand_total_emp_in_project = 0;
                    @endphp
                @foreach ($atten_night_manpower_records as $attendence)
                    @php   
                        $grand_total_present_emp +=$attendence->total_present_emp ;
                        $grand_total_emp_in_project +=$attendence->total_emp_in_project ;
                    @endphp
               
                  <tr style="text-align: center;">
                    <td class="td__s_n"> {{ $loop->iteration }}</td> 
                    <td class="td__trade_name"> {{ $attendence->catg_name}}</td>
                    <td class="td__amount"> {{ $attendence->total_emp_in_project }} </td>
                    <td class="td__amount"> {{ $attendence->total_present_emp }} </td>
                    <td class="td__amount"> {{ ($attendence->total_emp_in_project - $attendence->total_present_emp) <= 0 ? "-" : $attendence->total_emp_in_project - $attendence->total_present_emp}} </td>
                 </tr>
                @endforeach 

                <tr>
                    <td class="td__total_amount" colspan="2"></td> 
                    <td class="td__total_amount" > {{ $grand_total_emp_in_project == 0 ? '' : $grand_total_emp_in_project}}</td> 
                    <td class="td__total_amount"  > {{ $grand_total_present_emp == 0 ? '' : $grand_total_present_emp }}  </td>    
                    <td class="td__total_amount"  > {{ ($grand_total_emp_in_project - $grand_total_present_emp) <= 0 ? "-" : $grand_total_emp_in_project - $grand_total_present_emp }}  </td>               
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
