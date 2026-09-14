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
                size: A4 landscape;
                margin: 15mm 0mm 10mm 0mm;
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
            width: 90%;
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
            font-size: 11px;
            border: 1px ridge gray;
            font-weight: bold;
            padding: 5px;
            color: #000;
            text-transform:capitalize;
            font-family: "Times New Roman", Times, serif;
        }

        table td {
            text-align: center;
            font-size: 11px;
            border: 1px ridge gray;
            padding: 5px;
            margin: 0px;
            text-transform:capitalize;
            font-family: "Times New Roman", Times, serif;
            /* Top,Right,Bottom,left */
        }


        .td__s_n {
            font-size: 11px;
            color: black;
            text-align: center;

        }
 
        .td__project_name {
            font-size: 11px;
            color: black;
            font-weight: 100;
            text-align: left;
            text-transform:capitalize
        }       

        .td__amount {
            font-size:11px;
            color: black;
            font-weight: 100;
            text-align: center;
            padding-right:5px;
        }
        .td__total_amount {
            font-size:11px;
            color: black;
            font-weight: bold;
            text-align: center;
            padding-right:5px;
        }
         /* Table Row COlor on Hover */
         #employeeinfo {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        .table__part {
            display: flex;
        }


        #employeeinfo td,
        #employeeinfo th {
            border: 1px solid #ddd;
            font-size: 11px;
            padding: 5px;
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



    </style>
    <!-- style -->
</head>

<body>
    <div class="main__wrap">
        <!-- header part-->
        <section class="header__part"   >
            <!-- date -->
            <div class="title_left__part">
                <p> <strong> Employee Attendance Summary of  <br>
                    {{$report_title[0]}}  </strong></p>
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
        <h5 style="text-align: center">Basic And Hourly Employees Attendance Summary</h5>
        <!-- table part -->
        <section class="table__part">
             <table id="employeeinfo">
                <thead>
                <tr> 
                  <th colspan="2"> <span></span> </th>
                  <th colspan="3"> <span>Day Shift</span> </th>
                  <th colspan="3"> <span>Night Shift</span> </th>   
                  <th colspan="3"> <span>Total</span> </th>             
                </tr>
                <tr>
                    <th> <span>S.N</span> </th> 
                    <th> <span>Project Name</span> </th>
                    <th> <span>Basic</span> </th>
                    <th> <span>Hourly</span> </th>
                    <th> <span>Total</span> </th>

                    <th> <span>Basic</span> </th>
                    <th> <span>Hourly</span> </th>
                    <th> <span>Total</span> </th>

                    <th> <span>Total Basic</span> </th>
                    <th> <span>Total Hourly</span> </th>
                    <th> <span>Total Present</span> </th>
                    
                  </tr>


                </thead>
                <tbody>
                    @php 
                        $grand_total_basic = 0;
                        $grand_total_hourly = 0;                     
                        $grand_total_emp = 0;                    
                                         
                    @endphp

                  @foreach ($project_records as $record)
                  @php                       

                     $dayshift =  $record->day_shift_record;
                     
                     $day_basic_emp =  $dayshift != null ? $dayshift['total_basic_emp']: 0;
                     $day_hourly_emp =   $dayshift != null ? $dayshift['total_hourly_emp']: 0;
                     $day_total_emp =   $day_basic_emp + $day_hourly_emp;
              

                     $nightshift =  $record->night_shift_record;
                     $night_basic_emp = $nightshift != null ? $nightshift['total_basic_emp']: 0;
                     $night_hourly_emp =  $nightshift != null ? $nightshift['total_hourly_emp']: 0;
                     $night_total_emp =   $night_basic_emp + $night_hourly_emp;

                    // $project_emp =   $night_total_emp + $day_total_emp;
                     $grand_total_basic += $day_basic_emp + $night_basic_emp;
                     $grand_total_hourly += $day_hourly_emp + $night_hourly_emp ;

                     $grand_total_emp += $night_total_emp + $day_total_emp;
                  @endphp
                    
                <tr>
                  <td class="td__s_n"> {{ $loop->iteration }}</td>
                  <td class="td__project_name"> {{ $record->proj_name }} </td>
                  <td class="td__amount"> {{  $dayshift != null ? $dayshift['total_basic_emp'] :'-' }}</td>     
                  <td class="td__amount"> {{  $dayshift != null ? $dayshift['total_hourly_emp'] :'-' }}</td> 
                  <td class="td__amount"> {{  $day_total_emp > 0 ? $day_total_emp :'-' }}</td>            

                  <td class="td__amount"> {{  $nightshift != null ? $nightshift['total_basic_emp'] :'-' }}</td>     
                  <td class="td__amount"> {{  $nightshift != null ? $nightshift['total_hourly_emp'] :'-' }}</td>
                  <td class="td__amount"> {{  $night_total_emp > 0 ? $night_total_emp :'-' }}</td>            
                   
                  <td class="td__amount"> {{  ($day_basic_emp + $night_basic_emp ) > 0 ? $day_basic_emp + $night_basic_emp :'-' }}</td>
                  <td class="td__amount"> {{  ($day_hourly_emp + $night_hourly_emp ) > 0 ? $day_hourly_emp + $night_hourly_emp :'-' }}</td>   
                  <td class="td__amount"> {{  ($night_total_emp + $day_total_emp) > 0 ? ($night_total_emp + $day_total_emp) :'-' }}</td>     
                     
                  
                 </tr>
                @endforeach  
                  <tr> 
                    <td colspan="8" class="td__amount"> <b>Grand Total</b> </td>   
                    <td class="td__total_amount"> {{  $grand_total_basic }}</td>
                    <td class="td__total_amount"> {{  $grand_total_hourly}}</td>
                    <td class="td__total_amount"> {{  $grand_total_emp }}                    
                    </td>
                   </tr>  
                   <tr> 
                    <td colspan="8" class="td__amount">  </td>   
                    <td class="td__total_amount">Active : {{  $total_active_emps }}</td>
                    <td class="td__total_amount">Present:  {{  $grand_total_emp}}</td>
                    <td class="td__total_amount"> Absent : {{ $total_active_emps - $grand_total_emp}}
                    </td>
                   </tr>             
                
              </tbody>
            </table>
            
        </section>
        <!-- ---------- -->

    <section>
        <br><br>
        {{-- Officer Signature --}}
        <div class="row" style="padding-top: 10px;">
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
