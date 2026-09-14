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
 
        .td__project_name {
            font-size: 10px;
            color: black;
            font-weight: 100;
            text-align: left;
            text-transform:capitalize
        }       

        .td__amount {
            font-size:10px;
            color: black;
            font-weight: 100;
            text-align: right;
            padding-right:5px;
        }
        .td__total_amount {
            font-size:10px;
            color: black;
            font-weight: bold;
            text-align: right;
            padding-right:5px;
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
                <p> <strong> Project Monthly Hours Summary <br>
                    Month of {{$report_title[0]}}, {{$report_title[1]}} </strong></p>
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
                <thead>
                <tr> 
                  <th colspan="2"> <span></span> </th>
                  <th colspan="4"> <span>Day Shift</span> </th>
                  <th colspan="4"> <span>Night Shift</span> </th>   
                  <th colspan="4"> <span>Total</span> </th>             
                </tr>
                <tr>
                    <th> <span>S.N</span> </th> 
                    <th> <span>Project Name</span> </th>
                    <th> <span>Employee</span> </th>
                    <th> <span>Basic</span> </th>
                    <th> <span>Overtime</span> </th>
                    <th> <span>Total</span> </th>
                    <th> <span>Employee</span> </th>
                    <th> <span>Basic Hours</span> </th>
                    <th> <span>Overtime</span> </th>
                    <th> <span>Total</span> </th>

                    <th> <span>Total Emp.</span> </th>
                    <th> <span>Total Basic</span> </th>
                    <th> <span>Total Overtime</span> </th>
                    <th> <span>Basic+Overtime</span> </th>
                    
                  </tr>


                </thead>
                <tbody>
                    @php 
                        $grand_total_basic_hours = 0;
                        $grand_total_over_time = 0;
                     
                        $grand_total_emp = 0;
                        $grand_total_absent_emp = 0;                      
                  
                    @endphp

                  @foreach ($project_records as $record)
                  @php                       

                     $dayshift =  $record->day_shift_record;
                     $project_basic_hours =  $dayshift != null ? $dayshift->basic_hours: 0;
                     $project_ot_hours =  $dayshift != null ? $dayshift->over_time: 0;
                     $project_emp = $dayshift != null ? $dayshift->total_emp_worked: 0;


                     $nightshift =  $record->night_shift_record;
                     $project_basic_hours +=  $nightshift != null ? $nightshift->basic_hours: 0;
                     $project_ot_hours +=  $nightshift != null ? $nightshift->over_time: 0;
                     $project_emp += $nightshift != null ? $nightshift->total_emp_worked: 0;

                     $grand_total_basic_hours += $project_basic_hours;
                     $grand_total_over_time += $project_ot_hours;
                     $grand_total_emp += $project_emp;

                  @endphp
                    
                <tr>
                  <td class="td__s_n"> {{ $loop->iteration }}</td>
                  <td class="td__project_name"> {{ $record->proj_name }} </td>
                  <td class="td__amount"> {{  $dayshift != null ? $dayshift->total_emp_worked :'-' }}</td>            
                  <td class="td__amount"> {{ $dayshift != null ? number_format($dayshift->basic_hours, 2, '.', ',')  :'-'   }}</td> 
                  <td class="td__amount"> {{ $dayshift != null ? number_format($dayshift->over_time, 2, '.', ',') :'-'  }}</td> 
                  <td class="td__amount"> {{ $dayshift != null ? number_format(($dayshift->basic_hours + $dayshift->over_time),2,'.',','):'-'}}</td> 

                  <td class="td__amount"> {{ $nightshift != null ? $nightshift->total_emp_worked :'-' }}</td> 
                  <td class="td__amount"> {{ $nightshift != null ? number_format($nightshift->basic_hours, 2, '.', ',') :'-' }}</td> 
                  <td class="td__amount"> {{ $nightshift != null ? number_format($nightshift->over_time, 2, '.', ','):'-' }}</td> 
                  <td class="td__amount"> {{ $nightshift != null ? number_format(($nightshift->basic_hours + $nightshift->over_time),2,'.',','):'-'}}</td> 

                  <td class="td_total_amount"> {{   $project_emp }}</td>
                  <td class="td_total_amount"> {{   number_format($project_basic_hours,2,'.',',') }}</td>
                  <td class="td_total_amount"> {{   number_format($project_ot_hours,2,'.',',') }}</td>
                  <td class="td_total_amount"> {{  number_format(($project_basic_hours+$project_ot_hours),2,'.',',') }}</td>
                 </tr>
                @endforeach  
                <tr>
                    {{-- <td class="td__s_n">  </td>
                    <td class="td__project_name">  </td>
                    <td class="td__amount">  </td>            
                    <td class="td__amount">   </td> 
                    <td class="td__amount">    </td> 
                    <td class="td__amount"> </td> 
  
                    <td class="td__amount">  </td> 
                    <td class="td__amount"> </td> 
                    <td class="td__amount"> </td>  --}}
                    <td colspan="10" class="td__amount"> <b>Grand Total</b> </td>   
                    <td class="td__total_amount"> {{  $grand_total_emp }}</td>
                    <td class="td__total_amount"> {{   number_format($grand_total_basic_hours,2,'.',',') }}</td>
                    <td class="td__total_amount"> {{   number_format($grand_total_over_time,2,'.',',') }}</td>
                    <td class="td__total_amount"> {{  number_format(($grand_total_basic_hours+$grand_total_over_time),2,'.',',') }}</td>
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
